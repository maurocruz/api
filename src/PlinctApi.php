<?php
declare(strict_types=1);

namespace Plinct\Api;

use Plinct\Api\Middleware\CorsMiddleware;
use Plinct\Api\Middleware\GatewayMiddleware;
use Plinct\Api\Server\PDOConnect;
use Slim\App;
use Plinct\Api\Middleware\LoggedUserMiddleware;

class PlinctApi
{
    /**
     * @var App
     */
    protected App $slimApp;
    /**
     * @var string
     */
    public static string $ISSUER = "https://plinct.com.br";
    /**
     * @var string
     */
    public static string $JWT_SECRET_API_KEY = "202103emplenapandemia";
    /**
     * @var float|int
     */
    public static $JWT_EXPIRE = 60*60*24*7;

    public static string $soloineApi = "https://plinct.com.br/soloine";

    /**
     * @param App $slimApp
     */
    public function __construct(App $slimApp)
    {
        $this->slimApp = $slimApp;
    }

    /**
     * @param $driver
     * @param $host
     * @param $dbname
     * @param $username
     * @param $password
     * @param array $options
     */
    public function connect($driver, $host, $dbname, $username, $password, array $options = [])
    {
        PDOConnect::setUsername($username);
        PDOConnect::setPassword($password);
        PDOConnect::connect($driver, $host, $dbname, $username, $password, $options);
    }

    /**
     * @return mixed
     */
    public function run() {
			$this->slimApp
				->addMiddleware(new GatewayMiddleware())
				->addMiddleware(new LoggedUserMiddleware())
				->addMiddleware(new CorsMiddleware(["Content-type"=>"application/json", "Access-Control-Allow-Origin"=>"*"]));
			return ApiFactory::request()->routes()->home($this->slimApp);
    }
}
