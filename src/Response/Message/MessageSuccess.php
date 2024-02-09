<?php

declare(strict_types=1);

namespace Plinct\Api\Response\Message;

class MessageSuccess extends MessageAbstract
{
	public function __construct()
	{
		parent::setStatus('success');
	}

	/**
	 * @param string $message
	 * @param $data
	 * @return array
	 */
	public function success(string $message, $data = null): array
	{
		return parent::getReturns('0000',$message,$data);
	}
}
