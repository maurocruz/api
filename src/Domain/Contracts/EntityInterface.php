<?php
namespace Plinct\Api\Domain\Contracts;

interface EntityInterface
{
	const bool IS_MODULE = true;

	const array DEPENDENCIES = [];

	public function hydrateParams(array $data, bool $withParent = false): array;

	public function gettype(): string;

	public function getIdthing(): int;
}
