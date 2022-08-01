<?php

declare(strict_types=1);

namespace Plinct\Api\Response\Message;

abstract class MessageAbstract
{
	/**
	 * @var array
	 */
	private array $returns;

	/**
	 * @param string $status
	 */
	protected function setStatus(string $status): void
	{
		$this->returns['status'] = $status;
	}

	/**
	 * @param string $code
	 */
	private function setCode(string $code): void
	{
		$this->returns['code'] = $code;
	}

	/**
	 * @param string $message
	 */
	private function setMessage(string $message): void
	{
		$this->returns['message'] = $message;
	}

	/**
	 * @param mixed $data
	 */
	private function setData($data): void
	{
		$this->returns['data'] = $data;
	}

	/**
	 * @param string $code
	 * @param string $message
	 * @param null $data
	 * @return array
	 */
	public function getReturns(string $code, string $message, $data = null): array
	{
		$this->setCode($code);
		$this->setMessage($message);
		$this->setData($data);
		return $this->returns;
	}
}
