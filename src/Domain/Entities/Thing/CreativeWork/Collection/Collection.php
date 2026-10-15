<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\Collection;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class Collection extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	private int $thing;
	protected int $idcollection;
	protected int $creativeWork;

	public function __construct(array $value = ["@type" => "Collection"])
	{
		parent::__construct($value);

		$this->idcollection = $value['idcollection'];
		$this->creativeWork = $value['creativeWork'];
	}

	public function getId(): ?int
	{
		return $this->idcollection;
	}
}
