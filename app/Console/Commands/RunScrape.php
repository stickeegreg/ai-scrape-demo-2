<?php

namespace App\Console\Commands;

use App\Models\Scrape;
use App\Models\ScrapeRun;
use App\ProgressReporters\ConsoleProgressReporter;
use Illuminate\Console\Command;

class RunScrape extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-scrape {scrape}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a scrape run';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $scrape = Scrape::find($this->argument('scrape'));

        if (! $scrape) {
            $this->error('Scrape not found');

            return self::FAILURE;
        }

        /** @var \App\Models\ScrapeRun $scrapeRun */
        $scrapeRun = ScrapeRun::create([
            'scrape_id' => $scrape->id,
            'status' => 'running',
        ]);

        $this->info('Created scrape run ' . $scrapeRun->id . ' http://ai-scrape-demo-2.test/scrape-runs/' . $scrapeRun->id);

        $progressReporter = new ConsoleProgressReporter($this->output);

        $scrapeRun->run($progressReporter);

        $this->info('Finished scrape run ' . $scrapeRun->id . ' http://ai-scrape-demo-2.test/scrape-runs/' . $scrapeRun->id);

        return self::SUCCESS;
    }
}
