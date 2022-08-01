<?php

declare(strict_types=1);

namespace Plinct\Api\User;

use Plinct\Api\Server\GetData\GetData;
use Plinct\Api\Server\Relationship\Relationship;
use Plinct\PDO\Crud;
use Plinct\PDO\PDOConnect;

class User extends Crud
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
	 * @var array|string[]
	 */
	protected array $hasTypes = ['privileges'=>'user_privileges'];

	public function get(array $params): array
	{
		$dataUser = new GetData('user');
		$dataUser->setParams($params);
		$data = $dataUser->render();

		if (isset($params['properties']) && strpos($params['properties'],'privileges') !== false) {
			foreach ($data as $key => $valueData) {
				$relationshipData = new Relationship('user', $valueData['iduser'], 'user_privileges');
				$valueData['privileges'] = $relationshipData->read();
				$data[$key] = $valueData;
			}
		}

		return $data;
	}

	/**
   * @param array $params
   * @return string[][]
   */
  public function post(array $params): array
  {
		if ($params['password'] !== $params['repeatPassword']) {
			return ["status" => 'fail', "message" => "Password repeat is incorrect"];
		}

    if (strlen($params['name']) < 5 ) {
      return [ "status" => 'fail', "message" => "The name must be longer than 4 characters" ];
    }

    if (filter_var($params['email'], FILTER_VALIDATE_EMAIL) === false) {
      return [ "status" => 'fail', "message" => "Invalid email" ];
    }

    if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).*$#", $params['password'])) {
      return [ "status" => "fail", "message" => "Password must be at least 8 characters in length and must contain at least one number, one upper case letter, one lower case letter and one special character" ];
    }

    $params['password'] = password_hash($params['password'], PASSWORD_DEFAULT);

	  unset($params['repeatPassword']);
	  unset($params['token']);
	  unset($params['status']);

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
	 * @param $params
	 * @return string[]
	 */
	public function put($params): array
	{
		$iduser = $params['iduser'] ?? null;
		if ($iduser) {
			if ($params['new_password']) {
				$params['password'] = $params['new_password'];
				unset($params['new_password']);
			} else {
				unset($params['password']);
			}

			$data = parent::update($params, "`iduser`=$iduser");

			if ($data == []) {
				return ['status'=>'success','message'=>'Your PUT request has be attend', 'data'=>$data];
			} else {
				return ['status'=>'fail','data'=>$data];
			}
		}
		return ['status'=>'fail','message'=>'Sorry! Your PUT request could not be performed.'];
	}

	/**
	 * @param $params
	 * @return string[]
	 */
	public function delete($params): array
	{
		$data = parent::erase($params);
		if ($data['status'] == 'success') {
			return $data;
		}
		return ['status'=>'fail','message'=>'Sorry! Your DELETE request could not be performed.','data'=>$data];
	}

  /**
   * @param null $type
   * @return array
   */
  public function createSqlTable($type = null) : array
  {
	  $data = PDOConnect::run(file_get_contents(__DIR__.'/User.sql'));
	  if (array_key_exists("error", $data)) {
		  return $data;
	  }
	  return [ "message" => "Sql table for ".$type. " created successfully!" ];
  }
}
