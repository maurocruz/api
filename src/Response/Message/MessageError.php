<?php

declare(strict_types=1);

namespace Plinct\Api\Response\Message;

class MessageError extends MessageAbstract
{
	public function __construct()
	{
		parent::setStatus('error');
	}

	public function anErrorHasOcurred($data): array
	{
		return parent::getReturns('E0001','an error has occurred', $data);
	}
}