<?php
namespace Plinct\Api\Request\Configuration\Module;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Module
{
	const SQL_DIR = __DIR__.'/database/';

	/**
	 * @param array|null $params
	 * @return string[]
	 */
	public function install(?array $params = null): array
	{
		$name = $params['name'] ?? null;
		$email = $params['email'] ?? null;
		$password = $params['password'] ?? null;
		$passwordRepeat = $params['passwordRepeat'] ?? null;
		if ($name && $email && $password && $passwordRepeat) {
			// check if user table exists
			$userTableExists = ApiFactory::request()->server()->connectBd('user')->showTableStatus();
			if (isset($userTableExists['status']) && $userTableExists['status'] === 'fail') {
				$install = $this->installModule('user');
				if (isset($install['status']) && $install['status'] === 'success') {
					$dataRegister = ApiFactory::request()->user()->authentication()->register($params);
					if (isset($dataRegister['status']) && $dataRegister['status'] === 'success'){
						$iduser = $dataRegister['data']['iduser'];
						$paramsPrivileges = ['iduser' => $iduser, 'function' => '5', 'action' => 'crud', 'namespace' => 'all', 'userCreator'=>$iduser];
						$dataPrivileges = ApiFactory::request()->server()->connectBd('user_privileges')->created($paramsPrivileges);
						$dataRegister['data']['privileges'] = empty($dataPrivileges) ? $paramsPrivileges : ['status'=>'fail','message'=>'Privileges not set'];
					}
					return $dataRegister;
				} else {
					return ['status'=>'fail','message'=>'Unable to install user module'];
				}
			} else {
				return ['status'=>'fail','message'=>'Database already installed'];
			}
		} else {
			return ['status'=>'fail','message'=>'Mandatory fields (name, email, password and passwordRepeat) are missing'];
		}
	}


	/**
	 * @param ?string $name
	 * @return string[]
	 */
	public function installModule(?string $name): array
	{
		if (!$name) return ['message'=>'Module was not created! Name is null!'];
		$tableName = lcfirst($name);
		$checkTable = ApiFactory::request()->server()->connectBd($tableName)->showTableStatus();
		if ($checkTable['status'] === 'fail') {
			$sqlFile = self::SQL_DIR.lcfirst($name).".sql";
			if (file_exists($sqlFile)) {
				$data = PDOConnect::run(file_get_contents($sqlFile));
				if(empty($data)) {
					return ['status'=>'success','message'=>'Module has been created'];
				} else {
					return ['status'=>'fail','message'=>'fail','data'=>$data];
				}
			} else {
				return ['status'=>'fail','message'=>'Module was not created! SQL file does not exists'];
			}
		} else {
			return $checkTable;
		}
	}
}
