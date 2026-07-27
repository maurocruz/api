<?php
namespace Plinct\Api\Domain\Entities\Thing;

use JsonSerializable;
use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Configuration\ConfigurationDomain;
use Plinct\Api\Domain\Contracts\EntityInterface;
use ReflectionClass;

class Thing implements EntityInterface, JsonSerializable
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = [];

	protected int $idthing;
	public string $type;
	public ?string $additionalType = null;
	public ?string $alternateName = null;
	protected string $dateRegistered;
	protected string $lastModified;
	public ?string $description = null;
	public ?string $disambiguatingDescription = null;
	public ?string $image = null;
	public ?string $mainEntityOfPage = null;
	public string $name;
	public ?string $sameAs = null;
	public ?string $url = null;
	public array $identifier = [];
	public ?array $aggregateRating = null;

	public function __construct(array $value = ["@type" => "Thing"])
	{
		$this->type = $this->type ?? $value['type'] ?? $value["@type"] ?? "Thing";
		$this->name = $value['name'] ?? $value['@id'] ?? "Thing";
		$this->idthing = $value['idthing'] ?? 0;
		$this->additionalType = $value['additionalType'] ?? null;
		$this->alternateName = $value['alternateName'] ?? null;
		$this->dateRegistered = $value['dateRegistered'] ?? date('Y-m-d H:i:s');
		$this->lastModified = $value['lastModified'] ?? date('Y-m-d H:i:s');
		$this->description = $value['description'] ?? null;
		$this->disambiguatingDescription = $value['disambiguatingDescription'] ?? null;
		$this->image = $value['image'] ?? null;
		$this->mainEntityOfPage = $value['mainEntityOfPage'] ?? null;
		$this->sameAs = $value['sameAs'] ?? null;
		$this->url = $value['url'] ?? null;
		//$this->identifier = ($value['identifier'] ?? []);
		$this->identifier[] = ['@type' => 'PropertyValue', 'name' => 'idthing', 'value' => $this->idthing];
		$this->identifier[] = ['@type' => 'PropertyValue', 'name' => 'lastModified', 'value' => $this->lastModified];
		$this->identifier[] = ['@type' => 'PropertyValue', 'name' => 'dateRegistered', 'value' => $this->dateRegistered];


		// AGGREGATE RATING
		if(isset($value['idreview'])) {
			$this->aggregateRating = [
				'@type' => 'AggregateRating',
				'ratingValue' => $value['ratingValue'] ?? null,
				'reviewCount' => $value['reviewCount'] ?? null,
			];
		}
	}

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}

	public function getId(): int
	{
		return $this->idthing;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getDependencies(): array
	{
		return self::DEPENDENCIES;
	}

	public function jsonSerialize(): array
	{
		$data = [
			"@context" => "https://schema.org",
			"@type" => $this->type,
			"@id" => ConfigurationDomain::getHost() . "/schema/" . lcfirst($this->type) . DIRECTORY_SEPARATOR . $this->getId(),
		];

		$reflection = new ReflectionClass($this);

		foreach ($reflection->getProperties() as $property) {
			// EXCLUI PROPRIEDADES NAO INICIALIZADAS
			if (!$property->isInitialized($this)) {
				continue;
			}
			// EXCLUI PROPRIEDADES PRIVADAS
			if ($property->isPrivate()) {
				continue;
			}
			// EXCLUI PROPRIEDADES PROTEGIDAS DE PUBLICAS
			if ($property->isProtected()) {
				continue;
			}

			$value = $property->getValue($this);

			// SE O VALOR FOR NULL, PULA
			if ($value === null) {
				continue;
			}

			$name = $property->getName();

			if ($name === 'type') {
				$name = '@type';
			}

			$data[$name] = $value;
		}

		return $data;
	}
}
