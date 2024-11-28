<?php

declare(strict_types=1);

namespace Agelgil\MapPicker\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Agelgil\MapPicker\MapPicker
 */
class MapPicker extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Agelgil\MapPicker\MapPicker::class;
    }
}
