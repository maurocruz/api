<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Intangible\StructuredValue\ContactPoint\PostalAddress;

use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\ContactPoint\PostalAddress\PostalAddressCreateUseCase;
use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\ContactPoint\PostalAddress\PostalAddressUpdateUseCase;
use Plinct\Api\Support\SupportController;
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
		$sanitizedParams = SupportController::sanitizeParams('PostalAddress', $parsedBody);
		$databaseData = SupportController::returnDTO($this->postalAddressCreateUseCase->create($sanitizedParams));
		$response->getBody()->write(json_encode($databaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response->withHeader('Content-Type', 'application/json');
	}

	/**
	 * @throws ReflectionException
	 */
	public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parsedBody = $request->getParsedBody();
		$sanitizedParams = SupportController::sanitizeParams('PostalAddress', $parsedBody);
		$databaseData = SupportController::returnDTO($this->postalAddressUpdateUseCase->update($sanitizedParams));
		$response->getBody()->write(json_encode($databaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response->withHeader('Content-Type', 'application/json');
	}
}
