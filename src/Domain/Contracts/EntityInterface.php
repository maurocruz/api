<?php
namespace Plinct\Api\Domain\Contracts;

interface EntityInterface
{
	const bool IS_MODULE = true;
	const array DEPENDENCIES = [];

	public function getId(): ?int;

	public function jsonSerialize(): array;
}
