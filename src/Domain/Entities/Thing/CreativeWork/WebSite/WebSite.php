<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\WebSite;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class WebSite extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	private int $thing;
	protected int $idwebSite;
	protected int $creativeWork;

	public function __construct(array $value = ["@type" => "Thing"])
	{
		parent::__construct($value);

		$this->idwebSite = $value['idwebSite'];
		$this->creativeWork = $value['creativeWork'] ?? 0;
	}

	public function getId(): ?int
	{
		return $this->idwebSite;
	}
}
