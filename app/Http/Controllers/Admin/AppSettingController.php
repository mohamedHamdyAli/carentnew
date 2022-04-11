<?php

namespace App\Http\Controllers\Admin;

use App\Models\State;
use App\Models\AppSetting;
use Illuminate\Support\Str;
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

class AppSettingController extends Controller
{

    private static function deleteFile($path)
    {
        $full_path = parse_url($path);
        return \File::delete($full_path['path']);
    }


    public function create(Request $request)
    {
        $setting = AppSetting::create($request->except('car_legal_download_1', 'car_legal_download_2','version'));
        $path = public_path()."/"."pdfs/";
        $setting->when($request->has('rent_download_1'), function($q) use ($setting, $request, $path){
            $file  = $request->file('rent_download_1');
            $name = 'rent_download_'.$setting->version.'.pdf';

            //$setting->car_legal_download_1 ? $this->deleteFile($setting->getRawOriginal('car_legal_download_1')) : null;
            $file->move($path, $name);
            $setting->update(['car_legal_download_1' =>  'pdfs/'.$name]);

        })->when($request->has('rent_download_2'), function($q) use ($setting, $request, $path){
            $file  = $request->file('rent_download_2');
            $name = 'rent_download_'.($setting->version.'(2)').'.pdf';

           // $setting->car_legal_download_2 ? $this->deleteFile($setting->getRawOriginal('car_legal_download_2')) : null;
           $file->move($path, $name);
           $setting->update(['car_legal_download_2' =>  'pdfs/'.$name]);
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
