<?php

namespace App\Models;

use App\ScrapeTypes\ScrapeTypeFactory;
use App\ScrapeTypes\ScrapeTypeInterface;
use Illuminate\Database\Eloquent\Model;

class ScrapeType extends Model
{
    protected $fillable = ['name', 'prompt', 'type'];
}
