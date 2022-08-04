<?php

declare(strict_types=1);

namespace Plinct\Api\User;

use Plinct\Api\Interfaces\HttpRequestInterface;
use Plinct\Api\Server\GetData\GetData;
use Plinct\Web\Debug\Debug;

class Permissions implements HttpRequestInterface
{
	private int $idUser;
	private int $function = 1;
	private string $actions = 'r';


	public function get(array $params = null): array
	{
		$data = new GetData('user_permissions');
		$data->setParams($params);
		return $data->render();
	}


	public function post(array $params = []): array
	{
		Debug::dump($params);
		return [];
	}


	public function put(array $params): array
	{
		return [];
	}
	public function delete(array $params): array
	{
		return [];
	}

	/**
	 * @param int $function
	 */
	public function setFunction(int $function): void
	{
		$this->function = $function;
	}

	/**
	 * @return int
	 */
	public function getFunction(): int
	{
		return $this->function;
	}
}
