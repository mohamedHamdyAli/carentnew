<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppSetting;
use App\Helpers\CacheHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class AppSettingController extends Controller
{

    private static function deleteFile($path)
    {
        $full_path = parse_url($path);
        return \File::delete($full_path['path']);
    }


    public function create(Request $request)
    {
        $setting = AppSetting::create($request->except('rental_contract_file', 'vehicle_receive_file', 'vehicle_return_file', 'version'));
        $path = public_path() . "/" . "pdfs/";
        dd($request->all());
        $setting->when($request->has('rental_contract_file'), function ($q) use ($setting, $request, $path) {
            $file  = $request->file('rental_contract_file');
            $name = 'rent_download_' . $setting->version . '.pdf';
            $file->move($path, $name);
            $setting->update(['rental_contract_file' =>  'pdfs/' . $name]);
        })->when($request->has('vehicle_receive_file'), function ($q) use ($setting, $request, $path) {
            $file  = $request->file('vehicle_receive_file');
            $name = 'rent_download_' . ($setting->version . '(2)') . '.pdf';
            $file->move($path, $name);
            $setting->update(['vehicle_receive_file' =>  'pdfs/' . $name]);
        })->when($request->has('vehicle_return_file'), function ($q) use ($setting, $request, $path) {
            $file  = $request->file('vehicle_return_file');
            $name = 'rent_download_' . ($setting->version . '(2)') . '.pdf';
            $file->move($path, $name);
            $setting->update(['vehicle_return_file' =>  'pdfs/' . $name]);
        });
        cache()->tags(['app-settings'])->flush();

        return response($setting, Response::HTTP_CREATED);
    }

    public function getLatestVersion()
    {
        $data = cache()->tags(['app-settings'])->remember(CacheHelper::makeKey('app-settings_latest_version'), 600, function () {
            return AppSetting::latest("version")->first();
        });
        return $data;
    }
}
