<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Product extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'product';
    }
}
