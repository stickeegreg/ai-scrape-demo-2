<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scrape extends Model
{
    protected $fillable = ['website_id', 'scrape_type_id', 'url', 'prompt', 'strategy'];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function scrapeType()
    {
        return $this->belongsTo(ScrapeType::class);
    }
}
