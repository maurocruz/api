<?php

namespace Fwc\Api;

use Slim\App;
use Fwc\Api\Server\PDOConnect;

class FwcApi
{
    protected $slimApp;

    public function __construct(App $slimApp)
    {
        $this->slimApp = $slimApp;
    }

    public function connect($driver, $host, $dbname, $username, $password, $options = [])
    {
        PDOConnect::connect($driver, $host, $dbname, $username, $password, $options);
    }

    public function run()
    {
        $routers = require __DIR__.'/router.php';
        return $routers($this->slimApp);
    }
}