<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandModelResource;
use App\Models\BrandModel;
use Illuminate\Http\Request;

class BrandModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BrandModel  $brandModel
     * @return \Illuminate\Http\Response
     */
    public function show($brandModel)
    {
        //
        $lang = app()->getLocale() ?? 'en';
        $data = cache()->tags(['brands', 'models'])->rememberForever("brand_models.{$brandModel}" . '-' . $lang, function () use ($brandModel, $lang) {
            return new BrandModelResource(BrandModel::where('brand_id', $brandModel)->orderBy("name_{$lang}")->get());
        });

        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BrandModel  $brandModel
     * @return \Illuminate\Http\Response
     */
    public function edit(BrandModel $brandModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BrandModel  $brandModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BrandModel $brandModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BrandModel  $brandModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(BrandModel $brandModel)
    {
        //
    }
}
