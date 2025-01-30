<?php

use App\Http\Controllers\Api\ScrapesController;
use App\Http\Controllers\Api\ScrapeTypesController;
use App\Http\Controllers\Api\WebsitesController;
use Illuminate\Support\Facades\Route;

Route::resource('websites', WebsitesController::class);
Route::resource('scrapes', ScrapesController::class);
Route::resource('scrape-types', ScrapeTypesController::class);
