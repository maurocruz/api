<?php

declare(strict_types=1);

namespace Plinct\Api\Response\Message;

class Message
{

	public function error(): MessageError
	{
		return new MessageError();
	}
	/**
	 * @return MessageFail
	 */
	public function fail(): MessageFail
	{
		return new MessageFail();
	}

	/**
	 * @return MessageSuccess
	 */
	public function success(): MessageSuccess
	{
		return new MessageSuccess();
	}
}
