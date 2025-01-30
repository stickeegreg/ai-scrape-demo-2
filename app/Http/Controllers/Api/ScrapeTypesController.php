<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateScrapeTypeRequest;
use App\Http\Requests\UpdateScrapeTypeRequest;
use App\Models\ScrapeType;
use Illuminate\Http\Request;

class ScrapeTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ScrapeType::all();
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
    public function store(CreateScrapeTypeRequest $request)
    {
        $scrapeType = ScrapeType::create($request->validated());

        return $scrapeType;
    }

    /**
     * Display the specified resource.
     */
    public function show(ScrapeType $scrapeType)
    {
        return $scrapeType;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ScrapeType $scrapeType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScrapeTypeRequest $request, ScrapeType $scrapeType)
    {
        $scrapeType->update($request->validated());

        return $scrapeType;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScrapeType $scrapeType)
    {
        $scrapeType->delete();

        return ['id' => $scrapeType->id];
    }
}
