<?php

declare(strict_types=1);

namespace Plinct\Api\Response\Message;

class MessageFail extends MessageAbstract
{
	public function __construct()	{
		parent::setStatus('fail');
	}

	/** VALIDATE GROUP**/
	public function inputDataIsMissing(): array	{
		return parent::getReturns('FV001', 'input data is missing');
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
	public function notFoundInDatabase(string $property, $data = null): array	{
		return parent::getReturns('FD001', "$property not found in database", $data);
	}


}