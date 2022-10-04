<?php

namespace App\Http\Controllers\Admin;

use App\Models\State;
use App\Models\Category;
use App\Models\BrandModel;
use App\Helpers\CacheHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\TextUI\XmlConfiguration\Logging\TeamCity;

class CategoryController extends Controller
{
    //create model
    public function createCategory(CreateCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        cache()->tags(['vehicles', 'categories'])->flush();

        return response($category, Response::HTTP_CREATED);
    }

    public function getSingleCategory($id)
    {
        $data = cache()->tags(['categories'])->remember(CacheHelper::makeKey('categories_'.$id), 600, function () use ($id) {
            return DB::table('categories')->where('id', $id)
            ->select('id', 'name_en', 'name_ar', 'display_order', 'active')
            ->first();
        });

        return $data;
    }

    public function updateCategory(UpdateCategoryRequest $request, $id)
    {
        $category = Category::whereId($id)->firstOrFail();
        $category->update($request->validated());

        cache()->tags(['vehicles', 'categories'])->flush();

        return response($category, Response::HTTP_OK);
    }

}
