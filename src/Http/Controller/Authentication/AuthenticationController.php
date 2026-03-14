<?php
namespace Plinct\Api\Http\Controller\Authentication;

use Plinct\Api\Application\Authentication\AuthenticationUseCase;
use Plinct\Api\Application\Authentication\ChangePasswordUseCase;
use Plinct\Api\Application\Authentication\ResetPasswordUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Random\RandomException;

readonly class AuthenticationController
{
	public function __construct(
		private AuthenticationUseCase $authenticationUseCase,
		private ResetPasswordUseCase $resetPasswordUseCase,
		private ChangePasswordUseCase $changePasswordUseCase
	)	{ }

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function register(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parsedBody = $request->getParsedBody();
		$useCaseData = $this->authenticationUseCase->register($parsedBody);
		$response->getBody()->write(json_encode($useCaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function isValidToken(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$useCaseData = $this->authenticationUseCase->validateToken();
		$response->getBody()->write(json_encode($useCaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws RandomException
	 */
	public function resetPassword(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parsedBody = $request->getParsedBody();
		$useCaseData = $this->resetPasswordUseCase->resetPassword($parsedBody);
		$response->getBody()->write(json_encode($useCaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function changePassword(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parsedBody = $request->getParsedBody();
		$useCaseData = $this->changePasswordUseCase->changePassword($parsedBody);
		$response->getBody()->write(json_encode($useCaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
