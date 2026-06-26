<?php
namespace Plinct\Api\Infrastructure\Persistence\Database;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpTooManyRequestsException;

readonly class ConnectDataBase
{
	public function __construct(
		private ContainerInterface $container,
		private DatabaseActions    $databaseStructure
	)	{	}

	public function connect(ServerRequestInterface $request): void
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

		// CONECTAR COM BANCO DE DADOS
		$connect = $this->databaseStructure->connect($driver, $host, $database, $username, $password, $options);
		if(!ConnectDB::testConnection()) {
			throw new HttpBadRequestException($request,'database connection failed: '.$connect->getMessage());
		}
	}
}
