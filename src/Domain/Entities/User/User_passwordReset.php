<?php
namespace Plinct\Api\Domain\Entities\User;

class User_passwordReset
{
	public const IS_MODULE = false;
	public const DEPENDENCIES = ['User'];

	protected int $iduser;
	protected ?string $selector = null;
	protected ?string $token = null;
	protected ?string $expires = null;

	public function __construct(array $value = [])
	{
		$this->iduser = $value['iduser'] ?? null;
		$this->selector = $value['selector'] ?? null;
		$this->token = $value['token'] ?? null;
		$this->expires = $value['expires'] ?? null;
	}

	public function getId(): int
	{
		return $this->iduser;
	}
}
