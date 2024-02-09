<?php

declare(strict_types=1);

namespace Plinct\Api\Response\Message;

abstract class MessageAbstract
{
	protected array $message = [
		'FA001' => "Password repeat is incorrect",
		'FA002' => "The name must be longer than 4 characters",
		'FA003' => "Password must be at least 8 characters in length and must contain at least one number, one upper case letter, one lower case letter and one special character",
		'FA004' => "invalid token",
		'FD002' => "the return is empty",
		'FT001' => 'this type not exists'
	];

	/**
	 * @var array
	 */
	protected array $returns;

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
	protected function setMessage(string $message): void
	{
		$this->returns['message'] = $message;
	}

	/**
	 * @param mixed $data
	 */
	protected function setData($data): void
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

	public function withCode(string $code, $data = null): array
	{
		return $this->getReturns($code, $this->message[$code], $data);
	}
}
