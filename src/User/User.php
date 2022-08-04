<?php

declare(strict_types=1);

namespace Plinct\Api\User;

use Plinct\Api\User\Auth\Authentication;
use Plinct\Api\Interfaces\HttpRequestInterface;

use Plinct\PDO\PDOConnect;

class User
{


	public function authentication(): Authentication
	{
		return new Authentication();
	}

	public function httpRequest(): HttpRequestInterface
	{
		return new UserActions();
	}
	public function permissions(): HttpRequestInterface
	{
		return new Permissions();
	}

	public static function userLogged(): UserLogged
	{
		return new UserLogged();
	}

  public function createSqlTable($type = null) : array
  {
	  $data = PDOConnect::run(file_get_contents(__DIR__.'/User.sql'));
	  if (array_key_exists("error", $data)) {
		  return $data;
	  }
	  return [ "message" => "Sql table for ".$type. " created successfully!" ];
  }
}
