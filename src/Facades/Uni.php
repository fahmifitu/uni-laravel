<?php

namespace fahmifitu\UniLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class Uni extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'uni-laravel';
    }
}
