<?php
namespace Plinct\Api\Http\Controller;

class HomeController
{
	/**
	 * @return AuthenticatorController
	 */
	public static function Auth(): AuthenticatorController
	{
		return new AuthenticatorController();
	}
}
