<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\WebSite;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class WebSite extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	protected int $idwebSite;
	protected int $thing;
	protected int $creativeWork;

	public function __construct(array $value = ["@type" => "Thing"])
	{
		$this->idwebSite = $value['idwebSite'] ?? 0;
		if ($this->idwebSite !== 0) {
			$this->identifier[] = ['@type' => 'PropertyValue', 'name' => 'idwebSite', 'value' => $this->idwebSite];
		}
		$this->type = 'WebSite';

		parent::__construct($value);

		$this->thing = $value['thing'] ?? 0;
		$this->creativeWork = $value['creativeWork'] ?? 0;
	}

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data, $withParent);
	}
}
