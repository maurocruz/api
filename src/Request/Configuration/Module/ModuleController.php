<?php
namespace Plinct\Api\Request\Configuration\Module;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class ModuleController
{
	/**
	 * @param array|null $params
	 * @return string[]
	 */
	public function init(?array $params = null): array
	{
		$name = $params['name'] ?? null;
		$email = $params['email'] ?? null;
		$password = $params['password'] ?? null;
		$passwordRepeat = $params['passwordRepeat'] ?? null;
		if ($name && $email && $password && $passwordRepeat) {
			// check if table 'user' exists
			$userTableExists = ApiFactory::request()->server()->connectBd('user')->showTableStatus();
			if (isset($userTableExists['status']) && $userTableExists['status'] === 'fail') {
				// RUN SQL
				$install = self::runSql(__DIR__ . '/database/user.sql', 'User');
				if (isset($install['status']) && $install['status'] === 'success') {
					// REGISTER USER
					$dataRegister = ApiFactory::request()->user()->authentication()->register($params);
					if (isset($dataRegister['status']) && $dataRegister['status'] === 'success') {
						// GRANT PRIVILEGES
						$iduser = $dataRegister['data']['iduser'];
						$paramsPrivileges = ['iduser' => $iduser, 'function' => '5', 'action' => 'crud', 'namespace' => 'all', 'userCreator'=>$iduser];
						$dataPrivileges = ApiFactory::request()->server()->connectBd('user_privileges')->created($paramsPrivileges);
						$dataRegister['data']['privileges'] = empty($dataPrivileges) ? $paramsPrivileges : ['status'=>'fail','message'=>'Privileges not set','data'=>$dataPrivileges];
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
	 * @param string $name
	 * @return string[]
	 */
	public function installModule(string $name): array
	{
		$modules = new Modules();
		if (method_exists($modules, lcfirst($name))) {
			return $modules->$name();
		} else {
			return ['status'=>'fail','message'=>'Module not exists!'];
		}
	}

	/**
	 * @param string $moduleName
	 * @param array $dependencies
	 * @return string[]
	 */
	public static function installer(string $moduleName, array $dependencies = [] ): array
	{
		$modules = new Modules();
		foreach ($dependencies as $dependency) {
			if (method_exists($modules, lcfirst($dependency))) {
				$install = $modules->$dependency();
				if(isset($install['status']) && $install['status'] == 'fail') {
					return $install;
				}
			}
		}
		return self::runSql(__DIR__."/database/".lcfirst($moduleName).".sql", ucfirst($moduleName));
	}

	/**
	 * @param string $filename
	 * @param string $moduleName
	 * @return array|string[]
	 */
	private static function runSql(string $filename, string $moduleName): array
	{
		$data = PDOConnect::run(file_get_contents($filename));
		if (empty($data)) {
			return ['status'=>'success','message'=>"Module $moduleName was created!"];
		} else {
			return ['status' => 'fail', 'message' => "Module $moduleName was not created!", 'data' => $data];
		}
	}
}
