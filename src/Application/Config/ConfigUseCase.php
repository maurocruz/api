<?php
namespace Plinct\Api\Application\Config;

use Plinct\Api\Domain\Modules\ModuleDomain;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseStructure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ConfigUseCase
{
	private string $host;
	private array $modulesEnabled;

	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function __construct(ContainerInterface $container, private readonly DatabaseStructure $databaseStructure) {
		$settings = $container->get('settings');
		try {
			$settings = $container->get('settings');
		} catch (ContainerExceptionInterface $e) {
			error_log($e->getMessage());
		}
		// HOST
		$this->host = $settings['host'];
		// MODULES ENABLED
		$showTables = $this->databaseStructure->showTables();
		$modulesAvailable = ModuleDomain::getModulesAvailable();
		$arrayFilter = array_filter($showTables, function($item) use ($modulesAvailable) {
			return in_array(ucfirst($item), $modulesAvailable);
		});
		$this->modulesEnabled = array_map(fn($item) => ucfirst($item),$arrayFilter);
	}

	public function getModulesForGraph(array $graph): array
	{
		foreach (ModuleDomain::getModulesAvailable() as $module) {
			$graph[] = [
				"@type" => "Service",
				"@id" => "$this->host.api/$module",
				"name" => $module,
				"offers" => in_array($module, $this->modulesEnabled) ? "InStock" : "OutOfStock"
			];
		}
		return $graph;
	}

	/**
	 * @return string
	 */
	public function getHost(): string
	{
		return $this->host;
	}
}
