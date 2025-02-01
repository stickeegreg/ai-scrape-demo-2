<?php

namespace App\ProgressReporters;

interface ProgressReporterInterface
{
    public function reportPercent(float $progress): void;
    public function reportMessage(string $message): void;
    public function reportComplete(): void;
}
