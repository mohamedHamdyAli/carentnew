<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Brand;
use Illuminate\Support\Str;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Http\Requests\CreateBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{

    public static function deleteFile($path)
    {
        $full_path = parse_url($path);
        return \File::delete($full_path['path']);
    }

    public static function uploadFile($file, $name, $path, $ext)
    {
        $name = str_replace(' ', '_', $name);
        $timestamp = Carbon::now()->timestamp . Str::random(5);
        $filename = "{$name}_{$timestamp}.{$ext}";

        Image::make($file)->fit(119, 119, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->orientate()->encode($ext)->save(public_path() . '/' . $path . $filename);

        return $path . $filename;
    }

    //create feature
    public function createBrand(CreateBrandRequest $request)
    {
        $brand = Brand::create($request->except('logo'));


        $brand->when($request->has('logo'), function ($q) use ($brand, $request) {
            $newFile = $this->uploadFile($request->file('logo'), Str::random(10), 'imgs/brands/', 'png');
            $brand->update(['logo' =>  $newFile]);
        });

        cache()->tags(['brands'])->flush();

        return response($brand, Response::HTTP_CREATED);
    }

    public function getSingleBrand($id)
    {
        $data = cache()->tags(['brands'])->remember(CacheHelper::makeKey('brands_' . $id), 600, function () use ($id) {
            $base = url('') . '/';
            return DB::table('brands')->where('id', $id)
                ->select('id', 'name_en', 'name_ar', 'display_order', 'active', DB::raw("CONCAT('$base',logo) as logo"))
                ->first();
        });
        return $data;
    }

    public function updateBrand(UpdateBrandRequest $request, $id)
    {
        $brand = Brand::whereId($id)->firstOrFail();

        $brand->update($request->except('logo'));
        $brand->when($request->has('logo'), function ($q) use ($brand, $request) {
            $newFile = $this->uploadFile($request->file('logo'), $brand->name_en, 'imgs/brands/', 'png');
            $brand->logo ? $this->deleteFile($brand->getRawOriginal('logo')) : null;
            $brand->update(['logo' =>  $newFile]);
        });

        cache()->tags(['vehicles', 'brands', 'models'])->flush();

        return response($brand, Response::HTTP_OK);
    }
}
