<?php
namespace Plinct\Api\Application\Support;

class EntityDTO
{
	private array $returns = [];

	/**
	 * @param bool|int $status
	 */
	public function setStatus(bool|int $status): void
	{
		$this->returns['status'] = $status;
	}

	/**
	 * @param string $message
	 */
	public function setMessage(string $message): void
	{
		$this->returns['message'] = $message;
	}

	/**
	 * @param int|null|string $count
	 */
	public function setCount(int|string|null $count): void
	{
		$this->returns['count'] = $count;
	}

	/**
	 * @param array $data
	 */
	public function setData(array $data): void
	{
		$this->returns['data'] = $data;
	}

	public function ready(): array
	{
		return $this->returns;
	}
}
