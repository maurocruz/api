<?php
namespace Plinct\Api\Domain\Entities\Thing\Article;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class Article extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['CreativeWork'];

	protected int $idarticle;
	private int $thing;
	private int $creativeWork;
	public ?string $articleBody = null;
	public ?string $articleSection = null;
	public ?string $backstory = null;

	public function __construct(array $value = ['@type' => 'Article'])
	{
		$this->idarticle = $value['idarticle'] ?? 0;
		$this->type = 'Article';
		if ($this->idarticle !== 0) {
			$this->identifier[] = ['@type' => 'PropertyValue', 'name' => 'idarticle', 'value' => $this->idarticle];
		}

		parent::__construct($value);

		$this->articleBody = $value['articleBody'] ?? null;
		$this->articleSection = $value['articleSection'] ?? null;
		$this->backstory = $value['backstory'] ?? null;
	}
}
