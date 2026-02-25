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
	 * @param array $params
	 * @param array|null $uploadfiles
	 * @return array
	 */
	public function post(array $params, array $uploadfiles = null): array;

	/**
	 * @param array $params
	 * @return array
	 */
	public function put(array $params): array;

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array;
}
