<?php

namespace App\Models;

use App\Models\Scrape;
use App\ProgressReporters\ProgressReporterInterface;
use App\ScrapeStrategies\ScrapeStrategy;
use App\ScrapeStrategies\ScrapeStrategyFactory;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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

            $this->update(['status' => 'completed']);

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

    public function updateMessages(array $messages): void
    {
        // TODO change this to a custom format
        // TODO use addMessage instead of updateMessages
        foreach ($messages as $messageKey => $message) {
            if (!is_array($message['content'])) {
                continue;
            }

            foreach ($message['content'] as $contentKey => $content) {
                if (!is_array($content)) {
                    continue;
                }

                if ($content['type'] === 'image') {
                    $messages[$messageKey]['content'][$contentKey] = $this->contentImageDataToUrl($content);
                }

                if (is_array($content['content'] ?? null)) {
                    foreach ($content['content'] as $subContentKey => $subContent) {
                        if ($subContent['type'] === 'image') {
                            $messages[$messageKey]['content'][$contentKey]['content'][$subContentKey] = $this->contentImageDataToUrl($subContent);
                        }
                    }
                }
            }
        }

        $data = $this->data;
        $data['messages'] = $messages;
        $this->data = $data;
        $this->save();
    }

    private function contentImageDataToUrl(array $content): array
    {
        if ($content['source']['type'] !== 'base64') {
            throw new Exception('Unexpected image source type: ' . $content['source']['type']);
        }

        if ($content['source']['media_type'] !== 'image/png') {
            throw new Exception('Unexpected image media type: ' . $content['source']['media_type']);
        }

        $data = $content['source']['data'];
        $hash = md5($data);
        $path = "scrape-runs/{$this->id}/{$hash}.png";

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, base64_decode($data));
        }

        // TODO only store the path and convert to a URL in the Resource
        return [
            'type' => 'image_url',
            'image_url' => [
                'url' => Storage::disk('public')->url($path),
                'path' => $path,
            ],
        ];
    }
}
