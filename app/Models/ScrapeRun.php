<?php

namespace App\Models;

use App\Models\Scrape;
use App\ProgressReporters\ProgressReporterInterface;
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

            $scrapeStrategy = $this->scrape->class::factory($progressReporter);
            $data = $this->data;
            $data['no_vnc_address'] = $scrapeStrategy->getNoVncAddress();
            $this->data = $data;
            $this->save();

            $scrapeStrategy->scrape($this->scrape->url);

            $this->update(['status' => 'completed']);
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
