<?php

namespace App\ScrapeTypes;

interface ScrapeTypeInterface
{
    public function getPrompt(): string;
    public function getTools(): array;
    public function save(): void;
}
