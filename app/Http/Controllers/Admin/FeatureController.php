<?php

namespace App\Http\Controllers\Admin;

use App\Models\State;
use App\Models\Feature;
use App\Helpers\CacheHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFeatureRequest;
use App\Http\Requests\UpdateFeatureRequest;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\TextUI\XmlConfiguration\Logging\TeamCity;

class FeatureController extends Controller
{
    //create feature
    public function createFeature(CreateFeatureRequest $request)
    {
        $feature = Feature::create($request->validated());

        return response($feature, Response::HTTP_CREATED);
    }

    public function getSingleFeature($id)
    {
        $data = cache()->tags(['features'])->remember(CacheHelper::makeKey('features_'.$id), 600, function () use ($id) {
            return DB::table('features')->where('id', $id)
            ->select('id', 'name_en', 'name_ar', 'active')
            ->first();
        });
        return $data;
    }

    public function updateFeature(UpdateFeatureRequest $request, $id)
    {
        $feature = Feature::whereId($id)->firstOrFail();
        $feature->update($request->validated());

        cache()->tags(['features'])->flush();

        return response($feature, Response::HTTP_OK);
    }

}
