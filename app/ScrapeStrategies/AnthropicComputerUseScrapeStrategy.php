<?php

namespace App\ScrapeStrategies;

use Anthropic;
use Anthropic\Responses\Completions\CreateResponse;
use App\CommandExecutors\RemoteCommandExecutor;
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
        return new self(config('scrape.anthropic.api_key'), config('scrape.no_vnc_addresses')[0]);
    }

    public function __construct(
        private string $apiKey,
        private string $noVncAddress
    ) {
    }

    public function scrape(string $url): array
    {
        dump($url);
        $commandExecutor = new RemoteCommandExecutor('localhost:3000'); // TODO take from config
        $toolCollection = ToolCollection::create([
            new AnthropicComputerUseTool($commandExecutor, 1024, 768, 1), // TODO take from vnc config
            new SaveTextTool(),
        ]);

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

        $result = $client->messages()->create([
            'model' => 'claude-3-5-sonnet-20241022',
            'max_tokens' => 1024,
            'tool_choice' => ['type' => 'any'],
            'messages' => [
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
                            'text' => 'Please think of a single word and type it into the browser address bar then press enter.',
                        ],
                    ],
                ],
            ],
            'tools' => $tools,
        ]);

        dump($result);

        if ($result->stop_reason !== 'tool_use') {
            throw new Exception('Unexpected stop reason: ' . $result->stop_reason);
        }

        foreach ($result->content as $content) {
            if ($content->type === 'tool_use') {
                $toolResult = $toolCollection->run($content->name, $content->input);
                dump($toolResult);
            } else {
                dump('Unexpected content type:', $content->type);
                dump($content);
            }
        }

        return [];
    }

    public function getNoVncAddress(): string
    {
        return $this->noVncAddress;
    }
}
