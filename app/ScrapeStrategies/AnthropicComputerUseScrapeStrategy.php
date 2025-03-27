<?php

namespace App\ScrapeStrategies;

use Anthropic;
use App\CommandExecutors\RemoteCommandExecutor;
use App\DataRepository;
use App\Models\ScrapeRun;
use App\ProgressReporters\ProgressReporterInterface;
use App\ScrapeStrategies\ScrapeStrategyInterface;
use App\Tools\AnthropicComputerUseTool;
use App\Tools\SaveTextTool;
use App\Tools\Utils\ToolCollection;
use Exception;
use Illuminate\Support\Facades\Storage;

class AnthropicComputerUseScrapeStrategy implements ScrapeStrategyInterface
{
    public function __construct(
        private ProgressReporterInterface $progressReporter,
        private string $apiKey,
        private string $noVncAddress,
        private string $controlServiceAddress
    ) {
    }

    public function scrape(ScrapeRun $scrapeRun): array
    {
        dump($scrapeRun->scrape->url);
        $commandExecutor = new RemoteCommandExecutor($this->controlServiceAddress);
        $dataRepository = new DataRepository();
        $toolCollection = new ToolCollection([
            new AnthropicComputerUseTool($commandExecutor, 1024, 768, 1), // TODO take from vnc config
            new SaveTextTool($dataRepository),
        ]);

        $commandExecutor->execute('/home/stickee/start_recording.sh');
        $commandExecutor->execute('/home/stickee/start_chrome.sh ' . escapeshellarg($scrapeRun->scrape->url));

        // wait for the page to load
        // TODO: this should be done better
        sleep(5);

        // $toolCollection->run('computer', ['mouse_move', null, [100, 100]]);
        // $toolCollection->run('computer', ['type', 'This is a test']);
        $screenshotResult = $toolCollection->handle('computer', ['screenshot']);

        $client = Anthropic::factory()
            ->withApiKey(config('scrape.anthropic.api_key'))
            ->withHttpHeader('anthropic-version', '2023-06-01')
            ->withHttpHeader('anthropic-beta', 'computer-use-2024-10-22')
            ->make();

        $systemPrompt = <<<'TEXT'
        <SYSTEM_CAPABILITY>
        * You are utilising an Ubuntu virtual machine with internet access.
        * The Google Chrome browser is already installed and running, ready for you to interact with.
        * When viewing a page it can be helpful to zoom out so that you can see everything on the page.  Either that, or make sure you scroll down to see everything before deciding something isn't available.
        * When using your computer function calls, they take a while to run and send back to you.  Where possible/feasible, try to chain multiple of these calls all into one function calls request.
        </SYSTEM_CAPABILITY>

        Close all modal dialogs (popups) before proceeding with any actions. For example, click ACCEPT, X or CLOSE.
        You may need to scroll up or down to see products or buttons.
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
                        'text' => $scrapeRun->scrape->scrapeType->prompt . "\n\n" . $scrapeRun->scrape->prompt . "\n\n" . $scrapeRun->scrape->website->prompt,
                    ],
                ],
            ],
        ];

        $scrapeRun->updateMessages($messages);

        $i = 0;
        $maxRequests = 15; // TODO

        while (true) {
            echo "\n\n---------------------------------------------------------------------------\n\n";

            if ($i === $maxRequests) {
                throw new Exception('Reached max requests: ' . $maxRequests);
            }

            $i++;

            // TODO enable caching
            $result = $client->messages()->create([
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 1024,
                'tool_choice' => ['type' => 'auto', 'disable_parallel_tool_use' => false],
                'system' => $systemPrompt,
                'messages' => $messages,
                'tools' => $toolCollection->getJsonSchemas(),
            ]);

            if ($result->stop_reason === 'end_turn') {
                $messages[] = [
                    'role' => 'assistant',
                    'content' => [
                        'type' => 'text',
                        'text' => 'Finished (stop_reason: end_turn)',
                    ],
                ];

                $scrapeRun->updateMessages($messages);

                break;
            }

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

            $scrapeRun->updateMessages($messages);

            $toolResultContent = [];

            foreach ($result->content as $content) {
                if ($content->type === 'tool_use') {
                    $this->progressReporter->reportMessage('Execute ' . $content->name, $content->input);
                    $toolResult = $toolCollection->handle($content->name, $content->input);

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

                $scrapeRun->updateMessages($messages);
            }
        }

        $commandExecutor->execute('/home/stickee/stop_recording.sh');

        // TODO: Let the recording finish, do this a better way
        sleep(2);

        Storage::disk('public')->put('recording-' . $scrapeRun->id . '.webm', file_get_contents('http://' . $this->controlServiceAddress . '/get-recording'));

        $data = $scrapeRun->data;
        $data['recording'] = 'recording-' . $scrapeRun->id . '.webm';
        $scrapeRun->data = $data;
        $scrapeRun->save();

        return $dataRepository->getData();
    }

    public function getNoVncAddress(): string
    {
        return $this->noVncAddress;
    }
}
