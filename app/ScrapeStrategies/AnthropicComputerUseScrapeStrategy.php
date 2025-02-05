<?php

namespace App\ScrapeStrategies;

use Anthropic;
use App\CommandExecutors\RemoteCommandExecutor;
use App\Models\ScrapeRun;
use App\ProgressReporters\ProgressReporterInterface;
use App\ScrapeStrategies\ScrapeStrategyInterface;
use App\Tools\AnthropicComputerUseTool;
use App\Tools\SaveTextTool;
use App\Tools\ToolCollection;
use Exception;

class AnthropicComputerUseScrapeStrategy implements ScrapeStrategyInterface
{
    public static function factory(ProgressReporterInterface $progressReporter): ScrapeStrategyInterface
    {
        // TODO: this should take a VNC server address from the pool
        return new self($progressReporter, config('scrape.anthropic.api_key'), config('scrape.no_vnc_addresses')[0]);
    }

    public function __construct(
        private ProgressReporterInterface $progressReporter,
        private string $apiKey,
        private string $noVncAddress
    ) {
    }

    public function scrape(ScrapeRun $scrapeRun): array
    {
        dump($scrapeRun->scrape->url);
        $commandExecutor = new RemoteCommandExecutor('localhost:3000'); // TODO take from config
        $toolCollection = ToolCollection::create([
            new AnthropicComputerUseTool($commandExecutor, 1024, 768, 1), // TODO take from vnc config
            new SaveTextTool(),
        ]);

        // $commandExecutor->execute('DISPLAY=:1 google-chrome-stable \
        //     --no-sandbox \
        //     --disable-dev-shm-usage \
        //     --disable-gpu \
        //     --disable-software-rasterizer \
        //     --disable-extensions \
        //     --no-first-run \
        //     --no-default-browser-check \
        //     --remote-debugging-port=9222 \
        //     --disable-background-networking \
        //     --disable-background-timer-throttling \
        //     --disable-backgrounding-occluded-windows \
        //     --disable-client-side-phishing-detection \
        //     --disable-crash-reporter \
        //     --disable-features=Translate,MediaRouter \
        //     --mute-audio \
        //     --disable-sync \
        //     ' . escapeshellarg($scrapeRun->scrape->url)
        //     . ' &');

        // wait for the page to load
        // TODO: this should be done better
        sleep(5);

        // $toolCollection->run('computer', ['mouse_move', null, [100, 100]]);
        // $toolCollection->run('computer', ['type', 'This is a test']);
        $screenshotResult = $toolCollection->run('computer', ['screenshot']);

        $client = Anthropic::factory()
            ->withApiKey(config('scrape.anthropic.api_key'))
            ->withHttpHeader('anthropic-version', '2023-06-01')
            ->withHttpHeader('anthropic-beta', 'computer-use-2024-10-22')
            ->make();

        $tools = $toolCollection->map(function ($tool) {
            // TODO do this better
            if ($tool->getName() === 'computer') {
                return $tool->getInputSchema();
            }

            return [
                'name' => $tool->getName(),
                'description' => $tool->getDescription(),
                'input_schema' => $tool->getInputSchema(),
            ];
        })->values();

        $systemPrompt = <<<'TEXT'
        <SYSTEM_CAPABILITY>
        * You are utilising an Ubuntu virtual machine with internet access.
        * The Google Chrome browser is already installed and running, ready for you to interact with.
        * When viewing a page it can be helpful to zoom out so that you can see everything on the page.  Either that, or make sure you scroll down to see everything before deciding something isn't available.
        * When using your computer function calls, they take a while to run and send back to you.  Where possible/feasible, try to chain multiple of these calls all into one function calls request.
        </SYSTEM_CAPABILITY>
        TEXT;

        $messages = [
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'image',
                        'source' => [
                            'type' => 'base64',
                            'media_type' => 'image/png',
                            'data' => $screenshotResult->base64Image,
                        ],
                    ],
                    [
                        'type' => 'text',
                        'text' => 'Your task is to get article titles from the https://zebra-north.com website.
The website is already loaded in Chrome.
Please browse to the c++ articles page and get the title of the first 5 articles.
When you have them, use the save_text tool to save each one individually.',
                    ],
                ],
            ],
        ];

        $i = 0;
        $maxRequests = 5; // TODO

        while ($i < $maxRequests) {
            echo "\n\n---------------------------------------------------------------------------\n\n";
            $i++;

            $data = $scrapeRun->data;
            $data['messages'] = $messages;
            $scrapeRun->data = $data;
            $scrapeRun->save();

            // TODO enable caching
            $result = $client->messages()->create([
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 1024,
                'tool_choice' => ['type' => 'auto', 'disable_parallel_tool_use' => false],
                'system' => $systemPrompt,
                'messages' => $messages,
                'tools' => $tools,
            ]);

            dump($result->content);

            if ($result->stop_reason !== 'tool_use') {
                throw new Exception('Unexpected stop reason: ' . $result->stop_reason);
            }

            $this->progressReporter->reportMessage('ACTIONS: ' . implode(', ', array_filter(array_map(fn ($c) => $c->type === 'tool_use' ? ($c->name === 'computer' ? ($c->input['action'] ?? 'MALFORMED') : $c->name) : null, $result->content))));

            $messages[] = [
                'role' => 'assistant',
                'content' => array_map(function ($c) {
                    return match ($c->type) {
                        'text' => ['type' => 'text', 'text' => $c->text],
                        'tool_use' => ['type' => 'tool_use', 'id' => $c->id, 'name' => $c->name, 'input' => $c->input],
                        default => ['type' => 'text', 'text' => 'Unexpected content type: ' . $c->type],
                    };
                }, $result->content),
            ];

            $data = $scrapeRun->data;
            $data['messages'] = $messages;
            $scrapeRun->data = $data;
            $scrapeRun->save();

            $toolResultContent = [];

            foreach ($result->content as $content) {
                if ($content->type === 'tool_use') {
                    $this->progressReporter->reportMessage('Execute ' . $content->name, $content->input);
                    $toolResult = $toolCollection->run($content->name, $content->input);

                    if ($toolResult->error) {
                        throw new Exception($toolResult->error);
                    }

                    if ($toolResult->base64Image) {
                        $screenshotResult = $toolResult;
                    }

                    $resultContent = [];

                    if ($toolResult->error) {
                        $resultContent = ($toolResult->system ? "<system>{$toolResult->system}</system>" : '') . $toolResult->error;
                    } else {
                        if ($toolResult->output) {
                            $resultContent[] = [
                                'type' => 'text',
                                'text' => ($toolResult->system ? "<system>{$toolResult->system}</system>" : '') . $toolResult->output,
                            ];
                        }

                        if ($toolResult->base64Image) {
                            $resultContent[] = [
                                'type' => 'image',
                                'source' => [
                                    'type' => 'base64',
                                    'media_type' => 'image/png',
                                    'data' => $toolResult->base64Image,
                                ],
                            ];
                        }
                    }

                    $toolResultContent[] = [
                        'type' => 'tool_result',
                        'content' => $resultContent,
                        'tool_use_id' => $content->id,
                        'is_error' => $toolResult->error ? true : false,
                    ];

                    sleep(2);
                } elseif ($content->type === 'text') {
                    $this->progressReporter->reportMessage('TEXT:', var_export($content->text, true));
                } else {
                    $this->progressReporter->reportMessage('Unexpected content type: ' . $content->type);
                    dump($content);
                }
            }

            if ($toolResultContent) {
                $messages[] = [
                    'role' => 'user',
                    'content' => $toolResultContent,
                ];
            }
        }

        return [];
    }

    public function getNoVncAddress(): string
    {
        return $this->noVncAddress;
    }
}
