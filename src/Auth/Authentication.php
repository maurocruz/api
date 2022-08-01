<?php

declare(strict_types=1);

namespace Plinct\Api\Auth;

use Exception;

class Authentication
{
	/**
	 * @param array $bodyParams
	 * @return array
	 */
  public static function login(array $bodyParams): array
  {
    return (new AuthController())->login($bodyParams);
  }

	/**
	 * @throws Exception
	 */
	public static function resetPassword(array $parseBody): array
  {
    return (new ResetPassword)->resetPassword($parseBody);
  }

  /**
   * @param array $bodyParams
   * @return array
   */
  public static function changePassword(array $bodyParams): array
  {
    return ResetPassword::changePassword($bodyParams);
  }
}
