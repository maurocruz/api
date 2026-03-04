<?php

namespace Plinct\Api\Http\Middleware;

use HttpInvalidParamException;
use Plinct\Api\Domain\Configuration\ConfigurationDomain;
use Plinct\Api\Domain\Modules\ModuleDomain;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class GlobalMiddleware implements MiddlewareInterface
{
	public function __construct(private ContainerInterface $container, private ConfigurationDomain $configurationDomain, private DatabaseActions $databaseStructure)
	{
	}

	/**
	 * @inheritDoc
	 * @throws HttpInvalidParamException
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		try {
			$settings = $this->container->get('settings');
		} catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
			throw new HttpInvalidParamException('settings not found');
		}
		$settingsDb = $settings['db'] ?? null;
		if ($settingsDb) {
			$this->databaseStructure->connect($settingsDb['driver'], $settingsDb['host'], $settingsDb['name'], $settingsDb['user'], $settingsDb['pass'], $settingsDb['options'] ?? []);
		}
		// SET MODULES ENABLED
		$showTables = $this->databaseStructure->showTables();
		$modulesAvailable = ModuleDomain::getModulesAvailable();
		$arrayFilter = array_filter($showTables, function($item) use ($modulesAvailable) {
			return in_array(ucfirst($item), $modulesAvailable);
		});
		$modulesEnabled = array_map(fn($item) => ucfirst($item),$arrayFilter);
		ModuleDomain::setModulesEnabled($modulesEnabled);
		// SET CONFIGURATION
		$schema = $request->getUri()->getScheme();
		$host = $request->getUri()->getHost();
		$this->configurationDomain->setHost($schema."://".$host);

		return $handler->handle($request);
	}
}
