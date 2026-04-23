<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\Certification;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class Certification extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	protected int $idcertification;
	protected int $thing;
	protected int $creativeWork;
	public ?string $auditDate = null;
	public string $certificationIdentification;
	public int $certificationStatus = 0;
	public ?string $datePublished = null;
	public ?string $expires = null;
	public int $issuedBy;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
