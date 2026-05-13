<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\Book;

use Plinct\Api\Application\Entities\Thing\CreativeWork\Book\BookReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class BookController
{
	public function __construct(
		private BookReadUseCase $bookReadUseCase
	)	{	}

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$dataUseCase = $this->bookReadUseCase->read($queryParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode(SupportApplication::returnDTOFromDatabaseData($dataUseCase), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
