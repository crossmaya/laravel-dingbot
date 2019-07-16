<?php

namespace Jt\DingBot\Facades;

use Illuminate\Support\Facades\Facade;

class Client extends Facade
{
    protected static function getFacadeAccessor() : string
    {
        return 'dingbot';
    }
}