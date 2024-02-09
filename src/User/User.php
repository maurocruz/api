<?php

declare(strict_types=1);

namespace Plinct\Api\User;

use Plinct\Api\Request\Actions\Actions;
use Plinct\Api\Request\HttpRequest;
use Plinct\Api\User\Privileges\Privileges;
use Plinct\Api\User\Auth\Authentication;
use Plinct\Api\User\Permission\Permissions;
use Plinct\PDO\PDOConnect;

class User
{
	public function authentication(): Authentication
	{
		return new Authentication();
	}

	public function actions(): Actions
	{
		return new Actions();
	}

	public function httpRequest(): HttpRequest
	{
		return new HttpRequest(new UserActions());
	}

	public function permissions(): Permissions
	{
		return new Permissions();
	}

	public function privileges(): Privileges
	{
		return new Privileges();
	}

	/**
	 * @return UserLogged
	 */
	public function userLogged(): UserLogged {
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
