<?php

declare(strict_types=1);

namespace Plinct\Api;

use Slim\App;

class PlinctApiFactory
{
    /**
     * @param App $slimApp
     * @return PlinctApi
     */
    public static function create(App $slimApp): PlinctApi {
        return new PlinctApi($slimApp);
    }
}
