<?php

declare(strict_types=1);

namespace Plinct\Api\Interfaces;

interface CrudInterface
{
	public function created(array $params): array;
	public function read(array $params): array;
	public function update(array $params): array;
	public function delete(array $params): array;
}