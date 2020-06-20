<?php

namespace Fwc\Api;

use Slim\App;

class FwcApiFactory
{
    public static function create(App $slimApp)
    {
        return new FwcApi($slimApp);
    }
}