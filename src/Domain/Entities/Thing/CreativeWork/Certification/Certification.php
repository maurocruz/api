<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\Certification;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class Certification extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	private ?int $thing;
	protected ?int $idcertification;
	protected ?int $creativeWork;
	public ?string $auditDate = null;
	public string $certificationIdentification;
	public int $certificationStatus = 0;
	public ?string $datePublished = null;
	public ?string $expires = null;
	public int $issuedBy;

	public function __construct(array $value = ["@type" => "Certification"])
	{
		parent::__construct($value);

		$this->idcertification = $value['idcertification'] ?? null;
		$this->creativeWork = $value['creativeWork'] ?? null;
		$this->auditDate = $value['auditDate'] ?? null;
		$this->certificationIdentification = $value['certificationIdentification'];
		$this->certificationStatus = $value['certificationStatus'] ?? 0;
		$this->datePublished = $value['datePublished'] ?? null;
		$this->expires = $value['expires'] ?? null;
		$this->issuedBy = $value['issuedBy'] ?? 0;
	}

	public function getId(): ?int
	{
		return $this->idcertification;
	}
}