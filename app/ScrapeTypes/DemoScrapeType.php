<?php

namespace App\ScrapeTypes;

use App\DataRepository;
use App\Models\ScrapeRun;
use App\Tools\SaveTextTool;

class DemoScrapeType implements ScrapeTypeInterface
{
    private readonly DataRepository $dataRepository;

    public function __construct(
        private readonly string $prompt,
        private readonly ScrapeRun $scrapeRun,
    ) {
        $this->dataRepository = new DataRepository();
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function getTools(): array
    {
        return [
            new SaveTextTool($this->dataRepository),
        ];
    }

    public function save(): void
    {
        $scrapeRun = $this->scrapeRun->fresh();

        $data = $scrapeRun->data;
        $data['result'] = $this->dataRepository->getData();
        $scrapeRun->data = $data;

        $scrapeRun->save();
    }
}
