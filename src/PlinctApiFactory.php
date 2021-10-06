<?php

declare(strict_types=1);

namespace Plinct\Api;

use Plinct\Api\Server\Request;
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

    /**
     * @param string $type
     * @return Request
     */
    public static function request(string $type): Request
    {
        return new Request($type);
    }
}
