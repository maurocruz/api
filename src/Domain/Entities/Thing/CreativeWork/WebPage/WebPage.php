<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\WebPage;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class WebPage extends CreativeWork
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	private ?int $thing;
	protected ?int $idwebPage;
	protected ?int $creativeWork;
	public ?string $breadcrumb = null;
	public ?int $primaryImageOfPage = null;

	public function __construct(array $value = ["@type" => "WebPage"])
	{
		parent::__construct($value);

		$this->idwebPage = $value['idwebPage'] ?? null;
		$this->creativeWork = $value['creativeWork'] ?? null;
		$this->breadcrumb = $value['breadcrumb'] ?? null;
		$this->primaryImageOfPage = $value['primaryImageOfPage'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idwebPage;
	}
}
