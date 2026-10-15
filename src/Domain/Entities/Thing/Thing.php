<?php
namespace Plinct\Api\Domain\Entities\Thing;

use JsonSerializable;
use Plinct\Api\Domain\Configuration\ConfigurationDomain;
use Plinct\Api\Domain\Contracts\EntityInterface;
use ReflectionClass;

class Thing implements EntityInterface, JsonSerializable
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = [];

	public string $type;
	public ?string $additionalType = null;
	public ?string $alternateName = null;
	public ?string $description = null;
	public ?string $disambiguatingDescription = null;
	public ?string $image = null;
	public ?string $mainEntityOfPage = null;
	public ?string $name = null;
	public ?string $sameAs = null;
	public ?string $url = null;
	public ?array $aggregateRating = null;
	protected ?int $idthing;
	protected ?string $dateRegistered = null;
	protected ?string $lastModified = null;
	private bool $isCount;
	private array $value;

	public function __construct(array $value = ["@type" => "Thing"])
	{
		$this->value = $value;
		$this->idthing = $value['idthing'] ?? null;
		$this->type = $this->type ?? $value['type'] ?? $value["@type"] ?? "Thing";
		$this->name = $value['name'] ?? $value['@id'] ?? null;
		$this->additionalType = $value['additionalType'] ?? null;
		$this->alternateName = $value['alternateName'] ?? null;
		$this->dateRegistered = $value['dateRegistered'] ?? null;
		$this->lastModified = $value['lastModified'] ?? null;
		$this->description = $value['description'] ?? null;
		$this->disambiguatingDescription = $value['disambiguatingDescription'] ?? null;
		$this->image = $value['image'] ?? null;
		$this->mainEntityOfPage = $value['mainEntityOfPage'] ?? null;
		$this->sameAs = $value['sameAs'] ?? null;
		$this->url = $value['url'] ?? null;

		// AGGREGATE RATING
		if(isset($value['idreview'])) {
			$this->aggregateRating = [
				'@type' => 'AggregateRating',
				'ratingValue' => $value['ratingValue'] ?? null,
				'reviewCount' => $value['reviewCount'] ?? null,
			];
		}

		$this->isCount =  (bool) preg_grep('/count\(.*\)/i', array_keys($value));
	}

	public function getId(): ?int
	{
		return $this->idthing;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		if ($this->isCount) {
			return $this->value;
		}

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

			$value = $property->getValue($this);
			// SE O VALOR FOR NULL, PULA
			if ($value === null) {
				continue;
			}
			$name = $property->getName();
			if ($name === 'type') {
				$name = '@type';
			}

			// SE A PROPRIEDADE PROTEGIDA COLOCA EM IDENTIFIER
			if ($property->isProtected()) {
				$data['identifier'][] = ["@type" => "PropertyValue", "name" => $name, "value" => $value];
			} else {
				$data[$name] = $value;
			}
		}

		// ORDENA O ARRAY DEIXANDO IDENTIFIER POR ÚLTIMO
		$identifier = $data['identifier'] ?? null;
		unset($data['identifier']);
		ksort($data);
		if ($identifier !== null) {
			$data['identifier'] = $identifier;
		}

		return $data;
	}
}
