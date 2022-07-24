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
        $oldSetting = $this->getLatestVersion();
        $setting = AppSetting::create(
            $request->except('rental_contract_file', 'vehicle_receive_file', 'vehicle_return_file', 'version')
        );
        $path = public_path() . "/" . "pdfs/";

        $this->uploadOrCopyOldFile('rental_contract_file', $setting, $oldSetting, $path, $request);
        $this->uploadOrCopyOldFile('vehicle_receive_file', $setting, $oldSetting, $path, $request);
        $this->uploadOrCopyOldFile('vehicle_return_file', $setting, $oldSetting, $path, $request);

        cache()->tags(['app-settings'])->flush();

        return response($this->getLatestVersion(), Response::HTTP_CREATED);
    }

    public function getLatestVersion()
    {
        $data = cache()->tags(['app-settings'])->remember(CacheHelper::makeKey('app-settings_latest_version'), 600, function () {
            return AppSetting::latest("version")->first();
        });
        return $data;
    }

    private function uploadOrCopyOldFile(string $fileName, AppSetting $setting, AppSetting $oldSetting = null, string $path, Request $request)
    {
        try {
            $name = $fileName . '_v' . str_pad($setting->version, 3, '0', STR_PAD_LEFT) . '.pdf';
            if ($request->has($fileName) && $request->file($fileName) != null) {
                $file  = $request->file($fileName);
                $file->move($path, $name);
                $setting->update([$fileName =>  'pdfs/' . $name]);
            } else if ($oldSetting->{$fileName} != null) {
                // copy old file with new version number
                $oldFile = str_replace(env('APP_URL'), '', $oldSetting->{$fileName});
                $oldFilePath = public_path($oldFile);
                $newFilePath = public_path('pdfs/' . $name);
                $newFilePath = public_path('pdfs/' . $name);
                $copied = copy($oldFilePath, $newFilePath);
                if ($copied) {
                    $setting->update([$fileName =>  'pdfs/' . $name]);
                }
            }
        } catch (\Exception $e) {
            // do nothing
        }
    }
}
