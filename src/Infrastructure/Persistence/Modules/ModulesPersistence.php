<?php
namespace Plinct\Api\Infrastructure\Persistence\Modules;

use Plinct\Api\Infrastructure\Persistence\Database\ConnectDB;

class ModulesPersistence
{

	public function installModule(string $moduleName): array
	{
		$sqlFile = __DIR__ . "/../Database/Structure/". lcfirst($moduleName) . ".sql";
		if (!file_exists($sqlFile)) {
			return ['status' => false, 'message' => 'File not found: ' . $sqlFile, 'data' => []];
		}
		$installData = ConnectDB::run(file_get_contents($sqlFile));
		if (empty($installData)) {
			return ['status' => true, 'message' => "Module $moduleName installed successfully", 'data' => $installData];
		} else {
			return ['status' => false, 'message' => 'Module not installed', 'data' => $installData];
		}
	}
}
