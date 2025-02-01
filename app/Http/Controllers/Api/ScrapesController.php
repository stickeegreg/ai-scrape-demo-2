<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateScrapeRequest;
use App\Http\Requests\UpdateScrapeRequest;
use App\Jobs\ScrapeJob;
use App\Models\Scrape;
use App\Models\ScrapeRun;
use Illuminate\Http\Request;

class ScrapesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Scrape::with(['website', 'scrapeType'])->get()->sortBy(['website.name', 'scrape_type.name']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateScrapeRequest $request)
    {
        $scrape = Scrape::create($request->validated());

        return $scrape;
    }

    /**
     * Display the specified resource.
     */
    public function show(Scrape $scrape)
    {
        return $scrape;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scrape $scrape)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScrapeRequest $request, Scrape $scrape)
    {
        $scrape->update($request->validated());

        return $scrape;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scrape $scrape)
    {
        $scrape->delete();

        return ['id' => $scrape->id];
    }

    public function run(Scrape $scrape)
    {
        $scrapeRun = ScrapeRun::create([
            'scrape_id' => $scrape->id,
            'status' => 'pending',
            'data' => [],
        ]);

        ScrapeJob::dispatch($scrapeRun);

        return $scrapeRun;
    }
}
