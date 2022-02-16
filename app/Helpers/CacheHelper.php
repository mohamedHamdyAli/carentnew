<?php

namespace App\Helpers;

class CacheHelper
{

    public static function makeKey($key)
    {
        $qs = str_replace(' ', '-', json_encode(request()->all()));
        $qs = str_replace('"', '', $qs);
        $qs = str_replace('{', '', $qs);
        $qs = str_replace('}', '', $qs);
        $qs = str_replace(':', '-', $qs);
        $qs = str_replace(',', '-', $qs);
        $qs = str_replace('[', '-', $qs);
        $qs = str_replace(']', '-', $qs);
        $qs = str_replace('/', '-', $qs);
        $qs = str_replace('\\', '-', $qs);
        return $key . '-' . $qs . '-' . app()->getLocale();
    }
}
