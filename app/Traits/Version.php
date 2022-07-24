<?php

namespace App\Traits;

trait Version
{
    protected static function bootVersion()
    {
        static::creating(function ($model) {
            if (!$model->version) {
                $model->version = $model->getNextVersion();
            }
        });
    }
}
