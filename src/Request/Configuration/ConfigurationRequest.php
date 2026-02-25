<?php
namespace Plinct\Api\Request\Configuration;

use Plinct\Api\ApiApp;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Configuration\Module\ModuleController;
use Plinct\Api\Request\Configuration\Update\Update;

class ConfigurationRequest
{

	public function index(): array
	{
		$dbName = ApiFactory::request()->configuration()->getDbName();
		$dataTables = ApiFactory::request()->server()->connectBd()->showTables();

		if (isset($dataTables['error'])) return $dataTables;

		return [
			"dbName" => $dbName,
			"numberOfTables" => count($dataTables),
			"tables" => $dataTables,
		];
	}

	public function getDbName(): string
	{
		return ApiApp::$DB_NAME;
	}

	/**
	 * @return Home
	 */
	public function home(): Home
	{
		return new Home();
	}

	/**
	 * @return ModuleController
	 */
	public function module(): ModuleController
	{
		return new ModuleController();
	}

	/**
	 * @return Update
	 */
	public function update(): Update
	{
		return new Update();
	}

	/**
	 * @return string
	 */
	public function getHost(): string
	{
		return ApiApp::$HOST ?? 'localhost';
	}
}
