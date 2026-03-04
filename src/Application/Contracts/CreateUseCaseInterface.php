<?php
namespace Plinct\Api\Application\Contracts;

interface CreateUseCaseInterface
{
	public function create(array $params = []): array;
}
