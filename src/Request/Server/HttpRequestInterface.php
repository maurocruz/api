<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server;

interface HttpRequestInterface
{
	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array;

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array;

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array;

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array;
}
