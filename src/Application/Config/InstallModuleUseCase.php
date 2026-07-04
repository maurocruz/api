<?php
namespace Plinct\Api\Application\Config;

use Plinct\Api\Domain\Entities\Entities;
use Plinct\Api\Domain\Modules\ModuleDomain;
use Plinct\Api\Infrastructure\Persistence\Modules\ModulesPersistence;

readonly class InstallModuleUseCase
{
	public function __construct(
		private ModulesPersistence $modulesPersistence
	) { }

	public function installModule(string $moduleName): array
	{
		// VERIFICAR A EXISTÊNCIA DO MÓDULO
		if (!ModuleDomain::moduleExists($moduleName)) {
			return ['status' => false, 'message' => 'Module not found', 'data' => []];
		}

		// VERIFICAR SE JÁ FOI INSTALADO
		if (ModuleDomain::moduleIsEnabled($moduleName)) {
			return ['status' => false, 'message' => 'The module has already been installed', 'data' => []];
		}

		// INSTALAR MÓDULO
		return $this->install($moduleName);
	}

	private function install(string $moduleName): array
	{
		$result = [];
		// VERIFICA SE HÁ DEPENDENCIAS
		$entity = Entities::getEntity($moduleName);
		foreach ($entity::DEPENDENCIES as $dependency) {
			// VERIFICA SE ESTA DEPENDENCIA EXISTE E ESTÁ INSTALADA
			if (!(Entities::hasEntity($dependency) && Entities::isActiveEntity($dependency))) {
				$installDependencyResult = $this->install($dependency);
				if ($installDependencyResult['status'] === false) {
					return ['status' => false, 'message' => 'Error installing module: ' . $dependency, 'data' => $installDependencyResult['data']];
				} else {
					$result[] = $installDependencyResult;
				}
			}
		}
		$installResult = $this->modulesPersistence->installModule($moduleName);
		if ($installResult['status'] === false) {
			return ['status' => false, 'message' => 'Error installing module: ' . $moduleName, 'data' => $installResult['data']];
		} elseif(!empty($result)) {
			return ['status' => true, 'message' => "Module $moduleName installed successfully with dependencies", 'data' => $result];
		} else {
			return ['status' => true, 'message' => "Module $moduleName installed successfully", 'data' => $installResult['data']];
		}
	}
}
