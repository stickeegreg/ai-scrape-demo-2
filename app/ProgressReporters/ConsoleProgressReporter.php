<?php

namespace App\ProgressReporters;

use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Helper\ProgressBar;

class ConsoleProgressReporter implements ProgressReporterInterface
{
    private ProgressBar $progressBar;
    private float $startTime;

    public function __construct(private OutputStyle $output)
    {
        $this->progressBar = $output->createProgressBar(100);
        $this->startTime = microtime(true);
    }

    public function reportPercent(float $progress): void
    {
        $this->progressBar->setProgress(floor($progress));
    }

    public function reportMessage(string $message): void
    {
        $this->output->writeln($message);
    }

    public function reportComplete(): void
    {
        $this->progressBar->finish();
        $elapsedTime = microtime(true) - $this->startTime;
        $timeString = now()->addSeconds($elapsedTime)->diffForHumans(now(), true);

        $this->output->info('Completed in ' . $timeString);
    }
}
