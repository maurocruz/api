<?php

declare(strict_types=1);

namespace Plinct\Api\Response\Message;

class MessageFail extends MessageAbstract
{
	public function __construct()	{
		parent::setStatus('fail');
	}

	/** VALIDATE DATA**/
	public function inputDataIsMissing($data = null): array	{
		return parent::getReturns('FV001', 'incomplete input data', $data);
	}

	public function invalidData(): array	{
		return parent::getReturns('FV002', 'invalid data');
	}

	public function invalidEmail(): array {
		return parent::getReturns('FV003', 'invalid email');
	}

	public function invalidDomain(): array	{
		return parent::getReturns('FV004', 'invalid domain');
	}
	public function invalidUrl(): array	{
		return parent::getReturns('FV005', 'invalid url');
	}

	/** DATABASE CHECK */
	public function propertyNotFoundInDatabase(string $property, $data = null): array	{
		return parent::getReturns('FD001', "$property not found in database", $data);
	}
	public function returnIsEmpty(): array {
		return parent::withCode('FD002');
	}

	/** USER */
	public function userDoesNotExist(): array {
		return parent::getReturns('FU001','user does not exist');
	}
	public function userExistsButNotLogged(): array {
		return parent::getReturns('FU002','The user exists but has not logged in. Check your password!');
	}
	public function userNotAuthorizedForThisAction($data = null): array	{
		return parent::getReturns('FU003', 'user logged is not authorized for this action', $data);
	}
	/** AUTHENTICATION */
	public function passwordRepeatIsIncorrect(): array {
		return parent::withCode('FA001');
	}
	public function nameLonger4Char(): array {
		return parent::withCode('FA002');
	}
	public function passwordLeastLength(): array {
		return parent::withCode('FA003');
	}
	public function invalidToken(): array {
		return parent::withCode('FA004');
	}
}
