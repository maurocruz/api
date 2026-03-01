<?php
namespace Plinct\Api\Application\Auth;

use Plinct\Api\ApiFactory;

class AuthLoginUseCase
{

	public function login(array $params): array
	{
		return ApiFactory::request()->user()->authentication()->login($params);
	}
}
