<?php
declare(strict_types=1);
namespace Plinct\Api\Response\Message;

class Message extends MessageAbstract
{
	/**
	 * @return MessageError
	 */
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
	 * @param string $message
	 * @param $data
	 * @return array
	 */
	public function success(string $message, $data = null): array
	{
		parent::setStatus('success');
		return parent::getReturns('0000',$message,$data);
	}
}
