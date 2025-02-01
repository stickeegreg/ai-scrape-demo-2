<?php

namespace App\Jobs;

use App\Models\ScrapeRun;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ScrapeJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private ScrapeRun $scrapeRun)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->scrapeRun->run();
    }
}
