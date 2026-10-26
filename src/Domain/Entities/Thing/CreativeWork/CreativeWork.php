<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork;

use Plinct\Api\Domain\Entities\Entities;
use Plinct\Api\Domain\Entities\Thing\Thing;

class CreativeWork extends Thing
{
	public const IS_MODULE = true;
	public const DEPENDENCIES = ['Thing'];

	protected ?int $idcreativeWork;
	private ?int $thing;
	public ?int $about = null;
	public ?string $acquireLicensePage = null;
	public ?string $alternativeHeadline = null;
	public ?string $author = null;
	public ?string $copyrightHolder = null;
	public ?string $creativeWorkStatus = null;
	public ?string $datePublished = null;
	public ?string $editor = null;
	public ?string $encodingFormat = null;
	public mixed $hasPart = null;
	public ?string $headline = null;
	public mixed $isPartOf = null;
	public string $keywords = '';
	public ?string $licence = null;
	public ?string $locationCreated = null;
	public ?string $maintainer = null;
	public ?int $position = null;
	public ?string $publisher = null;
	public ?string $size = null;
	public ?string $text = null;
	public ?string $thumbnail = null;
	public string $version = '';

	public function __construct(array $value = ["@type" => "Thing"])
	{
		parent::__construct($value);

		$this->idcreativeWork = $value['idcreativeWork'] ?? null;
		$this->about = $value['about'] ?? null;
		$this->acquireLicensePage = $value['acquireLicensePage'] ?? null;
		$this->alternativeHeadline = $value['alternativeHeadline'] ?? null;
		$this->author = $value['author'] ?? null;
		$this->copyrightHolder = $value['copyrightHolder'] ?? null;
		$this->creativeWorkStatus = $value['creativeWorkStatus'] ?? null;
		$this->datePublished = $value['datePublished'] ?? null;
		$this->editor = $value['editor'] ?? null;
		$this->encodingFormat = $value['encodingFormat'] ?? null;
		// HAS PART
		if (isset($value['hasPart']) && is_array($value['hasPart'])) {
			foreach ($value['hasPart'] as $valueHasPart) {
				$typeIsPartOf = $valueHasPart['typeIsPartOf'] ?? null;
				$idIsPartOf = $valueHasPart['idIsPartOf'] ?? null;
				if ($typeIsPartOf && $idIsPartOf) {
					$entity = Entities::getEntity($typeIsPartOf);
					$this->hasPart[] = (new $entity($valueHasPart))->jsonSerialize();
				}
			}
		} else {
			$this->hasPart = $value['hasPart'] ?? null;
		}
		// IS PART OF
		if (isset($value['isPartOf']) && is_array($value['isPartOf'])) {
			foreach ($value['isPartOf'] as $valueIsPartOf) {
				$typeIsPartOf = $valueIsPartOf['typeIsPartOf'] ?? null;
				$idIsPartOf = $valueIsPartOf['idIsPartOf'] ?? null;
				if ($typeIsPartOf && $idIsPartOf) {
					$entity = Entities::getEntity($typeIsPartOf);
					$this->isPartOf[] = (new $entity($valueIsPartOf))->jsonSerialize();
				}
			}
		} else {
			$this->isPartOf = $value['isPartOf'] ?? null;
		}
		$this->headline = $value['headline'] ?? null;
		$this->keywords = $value['keywords'] ?? '';
		$this->licence = $value['licence'] ?? null;
		$this->locationCreated = $value['locationCreated'] ?? null;
		$this->maintainer = $value['maintainer'] ?? null;
		$this->position = $value['position'] ?? null;
		$this->publisher = $value['publisher'] ?? null;
		$this->size = $value['size'] ?? null;
		$this->text = $value['text'] ?? null;
		$this->thumbnail = $value['thumbnail'] ?? null;
		$this->version = $value['version'] ?? '';
	}

	public function getId(): ?int
	{
		return $this->idcreativeWork;
	}

	public static function getChildren(): array
	{
		$returns = [];
		foreach (Entities::ENTITIES as $entity) {
			if ($entity['object'] === CreativeWork::class) continue;
			if (in_array('CreativeWork', $entity['object']::DEPENDENCIES)) {
				$returns[] = $entity['name'];
			}
		}
		return $returns;
	}
}
