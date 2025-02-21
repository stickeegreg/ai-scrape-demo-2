<?php

namespace App\Models;

use App\Models\Scrape;
use App\ProgressReporters\ProgressReporterInterface;
use App\ScrapeStrategies\ScrapeStrategy;
use App\ScrapeStrategies\ScrapeStrategyFactory;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapeRun extends Model
{
    protected $fillable = ['scrape_id', 'status'];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'data' => '[]',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function scrape(): BelongsTo
    {
        return $this->belongsTo(Scrape::class);
    }

    public function run(ProgressReporterInterface $progressReporter): void
    {
        try {
            $this->update(['status' => 'running']);

            $scrapeStrategyFactory = new ScrapeStrategyFactory();

            $scrapeStrategy = $scrapeStrategyFactory->create(ScrapeStrategy::from($this->scrape->strategy), $progressReporter);
            $data = $this->data;
            $data['no_vnc_address'] = $scrapeStrategy->getNoVncAddress();
            $this->data = $data;
            $this->save();

            $result = $scrapeStrategy->scrape($this);

            $this->refresh();
            $data = $this->data;
            $data['result'] = $result;
            $this->data = $data;
            $this->status = 'completed';
            $this->save();

            $progressReporter->reportComplete();
        } catch (Exception $e) {
            $this->status = 'failed';
            $data = $this->data;
            $data['error'] = $e->getMessage();
            $this->data = $data;
            $this->save();

            throw $e;
        }
    }
}
