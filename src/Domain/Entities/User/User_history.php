<?php
namespace Plinct\Api\Domain\Entities\User;

class User_history
{
	public const IS_MODULE = false;
	public const DEPENDENCIES = ['User'];

	protected int $iduser_history;
	protected ?int $iduser = null;
	protected string $method;
	protected ?string $summary = null;
	protected string $targetTable;
	protected int $targetId;

	public function __construct(array $value = [])
	{
		$this->iduser_history = $value['iduser_history'] ?? null;
		$this->iduser = $value['iduser'] ?? null;
		$this->method = $value['method'] ?? null;
		$this->summary = $value['summary'] ?? null;
		$this->targetTable = $value['targetTable'] ?? null;
		$this->targetId = $value['targetId'] ?? null;
	}

	public function getId(): int
	{
		return $this->iduser_history;
	}
}
