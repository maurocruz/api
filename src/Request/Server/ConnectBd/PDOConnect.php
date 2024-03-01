<?php
declare(strict_types=1);

namespace Plinct\Api\Request\Server\ConnectBd;

use Exception;
use PDO;
use PDOException;

class PDOConnect
{
  /**
   * @var PDO|null
   */
  private static ?PDO $PDOConnect = null;
  /**
   * @var ?PDOException
   */
  private static ?PDOException $ERROR = null;
  /**
   * @var string
   */
  private static string $DRIVER;
  /**
   * @var string
   */
  private static string $HOST;
  /**
   * @var string
   */
  private static string $DBNAME;
  /**
   * @var string
   */
  private static string $USERNAME;
  /**
   * @var string
   */
  private static string $PASSWORD;

  /**
   * @param $driver
   * @param $host
   * @param $dbname
   * @param $username
   * @param $password
   * @param array $options
   * @return Exception|PDO|PDOException
   */
  public static function connect($driver, $host, $dbname, $username, $password, array $options = [])
  {
    self::$DRIVER = $driver;
    self::$HOST = $host;
    self::$DBNAME = $dbname;
    self::$USERNAME = $username;
    self::$PASSWORD = $password;
    $default_options = [
      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => true
    ];
    $options = array_replace($default_options, $options);
    $dsn = $driver . ":host=" . $host . ";dbname=" . $dbname;
    try {
      $PDOConnect = new PDO($dsn, $username, $password, $options);
      self::$PDOConnect = $PDOConnect;
    } catch (PDOException $e) {
      self::$ERROR = $e;
    } finally {
      return self::$PDOConnect ?? self::$ERROR;
    }
  }

  /**
   * @return Crud
   */
  public static function crud(): Crud
  {
      return new Crud();
  }

  /**
   *
   */
  public static function disconnect()
  {
      self::$PDOConnect = null;
  }

  public static function testConnection(): bool
  {
    return (bool)self::$PDOConnect;
  }

  /**
   * @return array[]|null
   */
  public static function getError(): ?array
  {
    if (self::$ERROR) {
      return [ "error" => [
        "message" => self::$ERROR->getMessage(),
        "code" => self::$ERROR->getCode()
      ]];
    }
    return null;
  }

  /**
   * @return object|null
   */
  public static function getPDOConnect(): ?object
  {
    return self::$PDOConnect;
  }

  /**
   * @return string
   */
  public static function getDrive(): string
  {
    return self::$DRIVER;
  }

  /**
   * @return string
   */
  public static function getHost(): string
  {
    return self::$HOST;
  }

  /**
   * @return string
   */
  public static function getDbname(): string
  {
    return self::$DBNAME;
  }

  /**
   * @return string
   */
  public static function getUsername(): string
  {
    return self::$USERNAME;
  }

  /**
   * @return string
   */
  public static function getPassword(): string
  {
    return self::$PASSWORD;
  }

  /**
   * @param $username
   */
  public static function setUsername($username)
  {
    self::$USERNAME = $username;
  }

  /**
   * @param $password
   */
  public static function setPassword($password)
  {
    self::$PASSWORD = $password;
  }

  /**
   * @param $query
   * @param null $args
   * @return array[]
   */
  public static function run($query, $args = NULL): array
  {
    if(self::$PDOConnect) {
      $connect = self::$PDOConnect;
      try {
        if ($connect && $connect->errorCode() == '0000') {
          $q = $connect->prepare($query);
          $q->setFetchMode(PDO::FETCH_ASSOC);
          $q->execute($args);
          $errorInfo = $q->errorInfo();
          if ($errorInfo[0] == "0000") {
            return $q->fetchAll();
          } else {
            return ["error" => [
	            "message" => $errorInfo[2],
	            "code" => $errorInfo[0],
	            "query" => $query
            ]];
          }
        } else {
          throw new PDOException();
        }
      } catch (PDOException $e) {
        return ["error" => [
          "message" => $e->getMessage(),
          "code" => $e->getCode(),
          "query" => $query
        ]];
      }
    } else {
      return self::getError();
    }
  }

  /**
   * LAST INSERT ID
   */
  public static function lastInsertId(): int {
    $query = "SELECT LAST_INSERT_ID() AS id;";
    $return = self::run($query);
    return $return[0]['id'];
  }
}
