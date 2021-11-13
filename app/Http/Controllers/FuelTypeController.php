<?php

namespace App\Http\Controllers;

use App\Http\Resources\FuelTypeResoure;
use App\Models\FuelType;
use Illuminate\Http\Request;

class FuelTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = cache()->rememberForever("fuel-types", function () {
            return new FuelTypeResoure(FuelType::all());
        });

        return $data;
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
     * @param  \App\Models\FuelType  $fuelType
     * @return \Illuminate\Http\Response
     */
    public function show(FuelType $fuelType)
    {
        return [
            'message' => __('messages.r_success'),
            'data' => $fuelType,
            'error' => null,
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FuelType  $fuelType
     * @return \Illuminate\Http\Response
     */
    public function edit(FuelType $fuelType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FuelType  $fuelType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FuelType $fuelType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FuelType  $fuelType
     * @return \Illuminate\Http\Response
     */
    public function destroy(FuelType $fuelType)
    {
        //
    }
}
