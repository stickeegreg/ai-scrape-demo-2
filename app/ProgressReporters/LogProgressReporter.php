<?php

namespace App\ProgressReporters;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Log;

class LogProgressReporter implements ProgressReporterInterface
{
    private float $startTime;

    public function __construct(private OutputStyle $output)
    {
        $this->startTime = microtime(true);
    }

    public function reportPercent(float $progress): void
    {
        // Do nothing
    }

    public function reportMessage(string $message): void
    {
        Log::debug('ScrapeRun:' . $message);
    }

    public function reportComplete(): void
    {
        $elapsedTime = microtime(true) - $this->startTime;
        $timeString = now()->addSeconds($elapsedTime)->diffForHumans(now(), true);

        Log::debug('Completed in ' . $timeString);
    }
}
