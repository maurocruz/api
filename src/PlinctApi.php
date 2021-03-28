<?php
namespace Plinct\Api;

use Slim\App;
use Plinct\Api\Server\Maintenance;
use Plinct\PDO\PDOConnect;

class PlinctApi {
    protected App $slimApp;
    public static string $ISSUER = "https://plinct.com.br";
    public static string $JWT_SECRET_API_KEY = "202103emplenapandemia";
    public static int $JWT_EXPIRE = 60*60*24;

    public function __construct(App $slimApp) {
        $this->slimApp = $slimApp;
    }

    public function connect($driver, $host, $dbname, $username, $password, $options = []) {
        PDOConnect::setUsername($username);
        PDOConnect::setPassword($password);
        PDOConnect::connect($driver, $host, $dbname, $username, $password, $options);
    }

    public static function starApplication($params) {
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
            if (array_key_exists('error', $pdo)) {
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

    public function run() {
        $routers = require __DIR__ . '/router.php';
        return $routers($this->slimApp);
    }
}