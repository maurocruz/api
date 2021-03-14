<?php
namespace Plinct\Api;

use Slim\App;

class PlinctApiFactory
{
    public static function create(App $slimApp): PlinctApi {
        return new PlinctApi($slimApp);
    }
}