<?php
namespace Plinct\Api\Domain\Contracts;

interface EntityInterface
{
	const IS_MODULE = true;
	const DEPENDENCIES = [];

	public function getId(): ?int;

	public function jsonSerialize(): array;
}
