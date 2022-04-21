<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\PDO\PDOConnect;
use ReflectionException;

class User extends Entity implements TypeInterface
{
  /**
   * @var string
   */
  protected string $table = "user";
  /**
   * @var string
   */
  protected string $type = "User";
  /**
   * @var array|string[]
   */
  protected array $properties = [ "name", "status" ];

  /**
   * @param array $params
   * @return string[][]
   */
  public function post(array $params): array
  {
    if (strlen($params['name']) < 5 ) {
      return [ "status" => 'fail', "message" => "The name must be longer than 4 characters" ];
    }

    if (filter_var($params['email'], FILTER_VALIDATE_EMAIL) === false) {
      return [ "status" => 'fail', "message" => "Invalid email" ];
    }

    if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $params['password'])) {
      return [ "status" => "fail", "message" => "Password must be at least 8 characters in length and must contain at least one number, one upper case letter, one lower case letter and one special character" ];
    }

    $params['password'] = password_hash($params['password'], PASSWORD_DEFAULT);

    $data = parent::created($params);

    if (isset($data['error']) || (isset($data['status']) && $data['status'] == 'error')) {
			$message = isset($data['error']) ? $data['error']['message'] : ($data['message'] ?? "Unknown error");
      return [ 'status' => 'error', 'message'=>$message, 'data' => $data ];
    } else {
      $id = PDOConnect::lastInsertId();
      return ['status' => 'success', 'message' => 'User registered successfully', 'data' => ['iduser' => $id] ];
    }
  }

  /**
   * @param null $type
   * @return array
   * @throws ReflectionException
   */
  public function createSqlTable($type = null) : array
  {
    return parent::createSqlTable('User');
  }
}
