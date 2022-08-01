<?php

declare(strict_types=1);

namespace Plinct\Api\Auth;

class Validator
{
	public static function index(array $array, string $index)
	{
		return $array[$index] ?? false;
	}
	public static function isEmail(string $email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL);
	}
	public static function isDomain(string $domain)
	{
		return filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME);
	}

	public static function isUrl(string $url)
	{
		return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED | FILTER_NULL_ON_FAILURE);
	}

	public static function isPassword(string $password)
	{
		return filter_var($password);
	}
}
