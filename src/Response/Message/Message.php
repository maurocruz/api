<?php

declare(strict_types=1);

namespace Plinct\Api\Response\Message;

class Message
{
	/**
	 * @return MessageFail
	 */
	public static function fail(): MessageFail
	{
		return new MessageFail();
	}

	/**
	 * @return MessageSuccess
	 */
	public static function success(): MessageSuccess
	{
		return new MessageSuccess();
	}
}
