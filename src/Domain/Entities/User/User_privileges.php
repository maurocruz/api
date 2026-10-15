<?php
namespace Plinct\Api\Domain\Entities\User;

class User_privileges
{

	public const bool IS_MODULE = false;

	public const array DEPENDENCIES = ['User'];

	protected int $iduser_privileges;
	protected int $iduser;
	protected int $function = 1;
	protected string $action = 'r';
	protected string $namespace = '';
	protected ?int $userCreator = null;

	public function __construct(array $value = [])
	{
		$this->iduser_privileges = $value['iduser_privileges'] ?? null;
		$this->iduser = $value['iduser'] ?? null;
		$this->function = $value['function'] ?? null;
		$this->action = $value['action'] ?? null;
		$this->namespace = $value['namespace'] ?? null;
		$this->userCreator = $value['userCreator'] ?? null;
	}

	public function getId(): int
	{
		return $this->iduser_privileges;
	}
}
