<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\WebPageElement;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class WebPageElement extends CreativeWork
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	private ?int $thing;
	protected ?int $idwebPageElement;
	protected ?int $creativeWork;
	public ?string $cssSelector = null;

	public function __construct(array $value = ["@type" => "WebPageElement"])
	{
		parent::__construct($value);

		$this->idwebPageElement = $value["idwebPageElement"] ?? null;
		$this->creativeWork = $value["creativeWork"] ?? null;
		$this->cssSelector = $value["cssSelector"] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idwebPageElement;
	}

}
