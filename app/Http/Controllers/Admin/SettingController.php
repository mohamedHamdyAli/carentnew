<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Cache;
use DB;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function group($group)
    {
        $data = Cache::tags(['settings'])->rememberForever(CacheHelper::makeKey($group), function () use ($group) {
            $result = Setting::select(['id', 'group', 'name_en', 'name_ar'])->where('group', $group)->get();
            $result = $result->map(function ($item) {
                $item->makeHidden('content');
                $item->makeVisible('id');
                return $item;
            });
            return $result;
        });

        return [
            'data' => $data,
        ];
    }

    public function single($id)
    {
        $data = Cache::tags(['settings'])->rememberForever(CacheHelper::makeKey($id), function () use ($id) {
            $result = DB::table('settings')->where('id', $id)->first();
            $result->content_en = json_decode($result->content_en);
            $result->content_ar = json_decode($result->content_ar);
            return $result;
        });

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $data['content_en'] = $data['content_en'];
        $data['content_ar'] = $data['content_ar'];
        $result = Setting::findOrFail($id)->update($data);
        Cache::tags(['settings'])->flush();
        return response()->json($result);
    }
}
