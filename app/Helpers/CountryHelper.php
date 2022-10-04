<?php

namespace App\Helpers;

class CountryHelper
{
    public readonly string|null $code;
    public readonly string|null $id;

    public function __construct()
    {
        $this->code = request()->header('CountryCode');
        $this->id = request()->header('Country');
    }

    static function get()
    {
        return new self();
    }
}
