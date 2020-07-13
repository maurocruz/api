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

    public function connect($driver, $host, $dbname, $userPublic, $passwordPublic, $options = [])
    {
        PDOConnect::setUserPublic($userPublic);
        
        PDOConnect::setPasswordPublic($passwordPublic);
        
        PDOConnect::connect($driver, $host, $dbname, $userPublic, $passwordPublic, $options);
    }

    public function setAdminData($usernameAdmin, $emailAdmin, $passwordAdmin) 
    {
        PDOConnect::setUsernameAdmin($usernameAdmin);
        PDOConnect::setemailAdmin($emailAdmin);
        PDOConnect::setPasswordAdmin($passwordAdmin);
    }
    
    public function run()
    {
        $routers = require __DIR__.'/router.php';
        return $routers($this->slimApp);
    }
}