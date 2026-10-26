<?php
namespace Plinct\Api\Application\User;

use Plinct\Api\ApiFactory;
use Plinct\Api\Domain\User\UserLogged;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class UserLoggedUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	) { }

	/**
	 * LOGIN
	 *
	 * @param array $params
	 * @return array
	 */
	public function login(array $params): array
	{
		return ApiFactory::request()->user()->authentication()->login($params);
	}

	/**
	 * SAVE USER LOGGED
	 *
	 * @param object $tokenDecoded
	 * @return array
	 * @throws ReflectionException
	 */
	public function saveUserLogged(object $tokenDecoded) : array
	{
		$iduser = $tokenDecoded->uid;
		// OBTÉM USUÁRIO LOGADO
		$userLoggedData = $this->databaseActions->read('user',['iduser'=>$iduser,'fields'=>'iduser,name'])->render();
		if($userLoggedData['status'] === false) {
			return $userLoggedData;
		}
		$dataUser = $userLoggedData['data'][0];
		$iduser = $dataUser['iduser'];
		$name = $dataUser['name'];

		// OBTÉM PRIVILÉGIOS
		$privilegesData = $this->databaseActions->read('user_privileges',['iduser'=>$iduser, 'fields'=>'`function`,`action`,namespace,userCreator'])->render();
		if ($privilegesData['status'] === false) {
			return $privilegesData;
		}
		$privileges =  $privilegesData['data'];

		UserLogged::setId($iduser);
		UserLogged::setName($name);
		UserLogged::setPrivileges($privileges);

		return ['status'=> true, 'message'=> 'User logged', 'data'=>$dataUser];
	}
}
