<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Article;

use Plinct\Api\Application\Entities\Thing\Article\ArticleReadUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class ArticleController
{
	public function __construct(
		private ArticleReadUseCase $articleReadUseCase
	) {}

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('Article', $queryParams);
		$dataUseCase = SupportController::returnDTO($this->articleReadUseCase->read($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
