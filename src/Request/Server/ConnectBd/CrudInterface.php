<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server\ConnectBd;

interface CrudInterface
{
	/**
	 * @param array $params
	 * @return array
	 */
	public function created(array $params): array;

	/**
	 * @param array $params
	 * @return array
	 */
	public function read(array $params): array;

	/**
	 * @param array $params
	 * @return array
	 */
	public function update(array $params): array;

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array;
}
