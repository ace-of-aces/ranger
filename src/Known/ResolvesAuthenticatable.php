<?php

namespace Laravel\Ranger\Known;

use Illuminate\Contracts\Auth\Guard;
use Laravel\Ranger\Collectors\Models;
use Laravel\Ranger\Types\Type;

trait ResolvesAuthenticatable
{
    public static function user()
    {
        try {
            $guardModel = app(Guard::class)->getProvider()->getModel();
            $model = app(Models::class)->get($guardModel);

            if ($model) {
                return Type::union(Type::null(), Type::string($model->name));
            }
        } catch (\Throwable $e) {
            return Type::mixed();
        }
    }
}
