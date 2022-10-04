<?php

namespace App\Http\Controllers\Admin;

use App\Models\FuelType;
use App\Helpers\CacheHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFuelTypeRequest;
use App\Http\Requests\UpdateFuelTypeRequest;
use Symfony\Component\HttpFoundation\Response;

class FuelTypeController extends Controller
{
    //create feature
    public function createFuelType(CreateFuelTypeRequest $request)
    {
        $type = FuelType::create($request->validated());

        cache()->tags(['vehicles', 'fuel-types'])->flush();

        return response($type, Response::HTTP_CREATED);
    }

    public function getSingleFuelType($id)
    {
        $data = cache()->tags(['fuel-types'])->remember(CacheHelper::makeKey('fuel-types_'.$id), 600, function () use ($id) {
            return DB::table('fuel_types')->where('id', $id)
            ->select('id', 'name_en', 'name_ar', 'display_order', 'active')
            ->first();
        });
        return $data;
    }

    public function updateFuelType(UpdateFuelTypeRequest $request, $id)
    {
        $type = FuelType::whereId($id)->firstOrFail();
        $type->update($request->validated());

        cache()->tags(['vehicles', 'fuel-types'])->flush();

        return response($type, Response::HTTP_OK);
    }

}
