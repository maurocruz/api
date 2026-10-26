<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\WebSite;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class WebSite extends CreativeWork
{
	public const IS_MODULE = true;
	public const DEPENDENCIES = ['Thing','CreativeWork'];

	private ?int $thing;
	protected ?int $idwebSite;
	protected ?int $creativeWork;

	public function __construct(array $value = ["@type" => "Thing"])
	{
		parent::__construct($value);

		$this->idwebSite = $value['idwebSite'] ?? null;
		$this->creativeWork = $value['creativeWork'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idwebSite;
	}
}
