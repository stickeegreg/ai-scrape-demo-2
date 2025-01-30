<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $fillable = ['name', 'url', 'prompt'];
}
