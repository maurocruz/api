<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\Article;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class Article extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['CreativeWork'];

	private int $thing;
	protected int $idarticle;
	protected int $creativeWork;
	public ?string $articleBody = null;
	public ?string $articleSection = null;
	public ?string $backstory = null;

	public function __construct(array $value = ["@type" => "Article"])
	{
		parent::__construct($value);

		$this->creativeWork = $value['creativeWork'];
		$this->articleBody = $value['articleBody'] ?? null;
		$this->articleSection = $value['articleSection'] ?? null;
		$this->backstory = $value['backstory'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idarticle;
	}
}
