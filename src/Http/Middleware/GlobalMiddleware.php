<?php
namespace Plinct\Api\Http\Middleware;

use Plinct\Api\Domain\Configuration\ConfigurationDomain;
use Plinct\Api\Domain\Entities\Entities;
use Plinct\Api\Domain\Modules\ModuleDomain;
use Plinct\Api\Infrastructure\Persistence\Database\ConnectDB;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpTooManyRequestsException;

readonly class GlobalMiddleware implements MiddlewareInterface
{
	public function __construct(
		private ContainerInterface $container,
		private ConfigurationDomain $configurationDomain,
		private DatabaseActions $databaseStructure
	)	{	}

	/**
	 * @inheritDoc
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		// VERIFICAR CONEXÃO COM BANCO DE DADOS
		try {
			$settings = $this->container->get('settings');
		} catch (NotFoundExceptionInterface|ContainerExceptionInterface) {
			throw new HttpTooManyRequestsException($request,'settings not found: check if the file settings.php is in the root directory');
		}
		$driver = $_ENV['DB_DRIVER'] ?? $settings['db']['driver'] ?? null;
		$host = $_ENV['DB_HOST'] ?? $settings['db']['host'] ?? null;
		$database = $_ENV['DB_DATABASE'] ?? $settings['db']['database'] ?? null;
		$username = $_ENV['DB_USERNAME'] ?? $settings['db']['username'] ?? null;
		$password = $_ENV['DB_PASSWORD'] ?? $settings['db']['password'] ?? null;
		$options = $_ENV['DB_OPTIONS'] ?? $settings['db']['options'] ?? [];

		if (!$driver || !$host || !$database || !$username || !$password) {
			throw new HttpBadRequestException($request,'database connection not found: check if the file .env is in the root directory');
		}

		$connect = $this->databaseStructure->connect($driver, $host, $database, $username, $password, $options);
		if(!ConnectDB::testConnection()) {
			throw new HttpBadRequestException($request,'database connection failed: '.$connect->getMessage());
		}

		// SET MODULES ENABLED
		$showTables = $this->databaseStructure->showTables();
		Entities::setActiveEntities($showTables);
		$modulesAvailable = ModuleDomain::getNamesOfModulesAvailable();
		$arrayFilter = array_filter($showTables, function($item) use ($modulesAvailable) {
			return in_array(ucfirst($item), $modulesAvailable);
		});
		$modulesEnabled = array_map(fn($item) => ucfirst($item),$arrayFilter);
		ModuleDomain::setModulesEnabled($modulesEnabled);

		// SET CONFIGURATION
		$schema = $request->getUri()->getScheme();
		$host = $schema."://".$request->getUri()->getHost();
		$this->configurationDomain->setHost($settings['host'] ?? $host);

		return $handler->handle($request);
	}
}
