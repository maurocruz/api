<?php

declare(strict_types=1);

namespace Plinct\Api;

use PDO;
use Plinct\Api\Middleware\LoggedUserMiddleware;
use Plinct\Api\Request\RequestApi;
use Slim\App;
use Plinct\Api\Server\Maintenance;
use Plinct\PDO\PDOConnect;

class PlinctApi
{
    /**
     * @var App
     */
    protected $slimApp;
    /**
     * @var string
     */
    public static $ISSUER = "https://plinct.com.br";
    /**
     * @var string
     */
    public static $JWT_SECRET_API_KEY = "202103emplenapandemia";
    /**
     * @var float|int
     */
    public static $JWT_EXPIRE = 60*60*24*7;

    public static $soloineApi = "https://plinct.com.br/soloine";

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
     * @param $params
     * @return array|array[]|PDO|string[]|null
     */
    public static function starApplication($params)
    {
        $data = null;
        $userAdmin = $params['userAdmin'] ?? null;
        $emailAdmin = $params['emailAdmin'] ?? null;
        $passwordAdmin = $params['passwordAdmin'] ?? null;
        $dbName = $params['dbName'] ?? null;
        $dbUserName = $params['dbUserName'] ?? null;
        $dbPassword = $params['dbPassword'] ?? null;

        if ($userAdmin && $emailAdmin && $passwordAdmin && $dbUserName && $dbPassword) {
            $driver = PDOConnect::getDrive();
            $host = PDOConnect::getHost();
            PDOConnect::disconnect();
            $pdo = PDOConnect::connect($driver, $host, $dbName, $dbUserName, $dbPassword);

            if (array_key_exists('error', (array)$pdo)) {
                $data = $pdo;

            } elseif (is_object($pdo)) {
                $maintenance = new Maintenance();
                $data = $maintenance->start($userAdmin, $emailAdmin, $passwordAdmin);
            }

        } else {
            $data = [ "message" => "incomplete data" ];
        }

        return $data;
    }

    /**
     * @return mixed
     */
    public function run()
    {
			$this->slimApp->addMiddleware(new LoggedUserMiddleware());

			return RequestApi::routes()->home($this->slimApp);
    }
}
