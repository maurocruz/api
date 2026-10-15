<?php
namespace Plinct\Api\Domain\Entities\User;

class User
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = [];

	protected int $iduser;
	public string $name;
	public string $email;
	protected string $password;
	public string $dateCreated;
	public string $dateModified;

	public function __construct(array $data = [])
	{
		$this->iduser = $data['iduser'] ?? null;
		$this->name = $data['name'] ?? null;
		$this->email = $data['email'] ?? null;
		$this->password = $data['password'] ?? null;
		$this->dateCreated = $data['dateCreated'] ?? null;
		$this->dateModified = $data['dateModified'] ?? null;
	}

	public function getId(): int
	{
		return $this->iduser;
	}
}
