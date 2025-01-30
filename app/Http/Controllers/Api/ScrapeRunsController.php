<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScrapeRun;
use Illuminate\Http\Request;

class ScrapeRunsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ScrapeRun::with(['scrape.website', 'scrape.scrapeType'])->get();
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ScrapeRun $scrapeRun)
    {
        return $scrapeRun;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ScrapeRun $scrapeRun)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScrapeRun $scrapeRun)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScrapeRun $scrapeRun)
    {
        //
    }
}
