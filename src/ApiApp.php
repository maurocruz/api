<?php
namespace Plinct\Api;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Slim\App;

class ApiApp
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
	public static string $HOST;
	/**
	 * @var string
	 */
	public static string $DB_NAME;
	/**
	 * @var string
	 */
	public static string $DB_USER;
	/**
	 * @var string
	 */
	public static string $DB_HOST;

	/**
	 * @var string|null
	 */
	private static ?string $logdir = null;

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
  public function connect($driver, $host, $dbname, $username, $password, array $options = []): void
  {
		self::setDBNAME($dbname);
		self::setDBHOST($host);
		self::setDBUSER($username);
    PDOConnect::setUsername($username);
    PDOConnect::setPassword($password);
    PDOConnect::connect($driver, $host, $dbname, $username, $password, $options);
  }

	/**
	 * @param string $DB_NAME
	 */
	public static function setDBNAME(string $DB_NAME): void
	{
		self::$DB_NAME = $DB_NAME;
	}

	/**
	 * @return string
	 */
	public static function getDBNAME(): string
	{
		return self::$DB_NAME;
	}

	/**
	 * @param string $DB_HOST
	 */
	public static function setDBHOST(string $DB_HOST): void
	{
		self::$DB_HOST = $DB_HOST;
	}

	/**
	 * @return string
	 */
	public static function getDBHOST(): string
	{
		return self::$DB_HOST;
	}

	/**
	 * @param string $DB_USER
	 */
	public static function setDBUSER(string $DB_USER): void
	{
		self::$DB_USER = $DB_USER;
	}

	/**
	 * @return string
	 */
	public static function getDBUSER(): string
	{
		return self::$DB_USER;
	}

	/**
	 * @param string|null $logdir
	 */
	public function setLogdir(?string $logdir): void
	{
		self::$logdir = $logdir;
	}

	/**
	 * @return string|null
	 */
	public static function getLogdir(): ?string
	{
		return self::$logdir;
	}

  public function run(): void
  {
		$this->slimApp->run();
  }
}
