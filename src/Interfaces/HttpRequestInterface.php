<?php

declare(strict_types=1);

namespace Plinct\Api\Interfaces;

interface HttpRequestInterface
{
	public function get(array $params = []): array;

	public function post(array $params): array;

	public function put(array $params): array;

	public function delete(array $params): array;
}