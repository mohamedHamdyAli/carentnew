<?php

namespace App\Http\Controllers\Admin;

use App\Models\State;
use App\Models\BrandModel;
use App\Helpers\CacheHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateModelRequest;
use App\Http\Requests\CreateStateRequest;
use App\Http\Requests\UpdateModelRequest;
use App\Http\Requests\UpdateStateRequest;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\TextUI\XmlConfiguration\Logging\TeamCity;

class ModelController extends Controller
{
    //create model
    public function createModel(CreateModelRequest $request)
    {
        $model = BrandModel::create($request->validated());

        return response($model, Response::HTTP_CREATED);
    }

    public function getSingleModel($id)
    {
        $data = cache()->tags(['models'])->remember(CacheHelper::makeKey('models_'.$id), 600, function () use ($id) {
            return DB::table('brand_models as b')->where('b.id', $id)
            ->select('b.id', 'b.name_en', 'b.name_ar', 'b.brand_id', 'b.display_order','b.active','brands.name_en as brand_name_en','brands.name_ar as brand_name_ar')
            ->join('brands', 'brands.id', 'b.brand_id')
            ->first();
        });

        return $data;
    }

    public function updateModel(UpdateModelRequest $request, $id)
    {
        $model = BrandModel::whereId($id)->firstOrFail();
        $model->update($request->validated());

        cache()->tags(['models'])->flush();

        return response($model, Response::HTTP_OK);
    }

}
