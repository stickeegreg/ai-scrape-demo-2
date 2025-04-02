<?php

namespace App\ScrapeTypes;

use App\DataRepository;
use App\Tools\SaveTextTool;

class WalletPayPalScrapeType implements ScrapeTypeInterface
{
    private readonly DataRepository $dataRepository;

    public function __construct(
        private readonly string $prompt
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
        // TODO Implement the save logic for WalletPayPal scrape type
        dump($this->dataRepository);
    }
}
