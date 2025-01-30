<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapeType extends Model
{
    protected $fillable = ['name', 'prompt', 'fields'];
}
