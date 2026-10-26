<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\AudioObject;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\MediaObject;

class AudioObject extends MediaObject
{
	public const IS_MODULE = true;
	public const DEPENDENCIES = ['Thing','CreativeWork','MediaObject'];

	private ?int $thing;
	protected ?int $idaudioObject;
	protected ?int $creativeWork;
	protected ?int $mediaObject;

	public function __construct(array $value = ["@type" => "AudioObject"])
	{
		parent::__construct($value);

		$this->idaudioObject = $value['idaudioObject'] ?? null;
		$this->creativeWork = $value['creativeWork'] ?? null;
		$this->mediaObject = $value['mediaObject'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idaudioObject;
	}
}
