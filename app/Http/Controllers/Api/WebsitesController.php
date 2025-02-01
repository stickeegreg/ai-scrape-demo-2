<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateWebsiteRequest;
use App\Http\Requests\UpdateWebsiteRequest;
use App\Models\Website;
use Illuminate\Http\Request;

class WebsitesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Website::orderBy('name')->get();
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
    public function store(CreateWebsiteRequest $request)
    {
        $website = Website::create($request->validated());

        return $website;
    }

    /**
     * Display the specified resource.
     */
    public function show(Website $website)
    {
        return $website;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Website $website)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWebsiteRequest $request, Website $website)
    {
        $website->update($request->validated());

        return $website;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Website $website)
    {
        $website->delete();

        return ['id' => $website->id];
    }
}
