<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Entities;
use Plinct\Api\Domain\Entities\Thing\Thing;

class CreativeWork extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	protected int $idcreativeWork;
	private int $thing;
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
		$this->idcreativeWork = $value['idcreativeWork'] ?? 0;
		$this->type = $this->type ?? 'CreativeWork';
		if ($this->idcreativeWork !== 0) {
			$this->identifier[] = ['@type' => 'PropertyValue', 'name' => 'idcreativeWork', 'value' => $this->idcreativeWork];
		}

		parent::__construct($value);

		$this->about = $value['about'] ?? null;
		$this->acquireLicensePage = $value['acquireLicensePage'] ?? null;
		$this->alternativeHeadline = $value['alternativeHeadline'] ?? null;
		$this->author = $value['author'] ?? null;
		$this->copyrightHolder = $value['copyrightHolder'] ?? null;
		$this->creativeWorkStatus = $value['creativeWorkStatus'] ?? null;
		$this->datePublished = $value['datePublished'] ?? null;
		$this->editor = $value['editor'] ?? null;
		$this->encodingFormat = $value['encodingFormat'] ?? null;
		$this->hasPart = $value['hasPart'] ?? [];
		$this->headline = $value['headline'] ?? null;
		$this->isPartOf = $value['isPartOf'] ?? null;
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

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}

	public function getDependencies(): array
	{
		return self::DEPENDENCIES;
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
