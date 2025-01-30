<?php

use App\Http\Controllers\Api\ScrapeRunsController;
use App\Http\Controllers\Api\ScrapesController;
use App\Http\Controllers\Api\ScrapeTypesController;
use App\Http\Controllers\Api\WebsitesController;
use Illuminate\Support\Facades\Route;

Route::resource('websites', WebsitesController::class);
Route::resource('scrapes', ScrapesController::class);
Route::post('scrapes/{scrape}/run', [ScrapesController::class, 'run']);
Route::resource('scrape-types', ScrapeTypesController::class);
Route::resource('scrape-runs', ScrapeRunsController::class);
