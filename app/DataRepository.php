<?php

namespace App;

class DataRepository
{
    private array $data = [];

    public function addText(string $text): void
    {
        if (!isset($this->data['text'])) {
            $this->data['text'] = [];
        }

        $this->data['text'][] = $text;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
