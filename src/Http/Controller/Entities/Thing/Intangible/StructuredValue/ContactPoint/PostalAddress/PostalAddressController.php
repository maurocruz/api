<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Intangible\StructuredValue\ContactPoint\PostalAddress;

use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\ContactPoint\PostalAddress\PostalAddressCreateUseCase;
use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\ContactPoint\PostalAddress\PostalAddressUpdateUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class PostalAddressController
{
	public function __construct(
		private PostalAddressCreateUseCase $postalAddressCreateUseCase,
		private PostalAddressUpdateUseCase $postalAddressUpdateUseCase
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parsedBody = $request->getParsedBody();
		$databaseData = $this->postalAddressCreateUseCase->create($parsedBody);
		$response->getBody()->write(json_encode(SupportApplication::returnDTOFromDatabaseData($databaseData), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response->withHeader('Content-Type', 'application/json');
	}

	/**
	 * @throws ReflectionException
	 */
	public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parsedBody = $request->getParsedBody();
		$databaseData = $this->postalAddressUpdateUseCase->update($parsedBody);
		$response->getBody()->write(json_encode(SupportApplication::returnDTOFromDatabaseData($databaseData), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response->withHeader('Content-Type', 'application/json');
	}
}
