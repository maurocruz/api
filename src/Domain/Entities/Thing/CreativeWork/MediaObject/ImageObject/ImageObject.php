<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\ImageObject;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\MediaObject;

class ImageObject extends MediaObject
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing','CreativeWork','MediaObject'];

	private ?int $thing;
	protected ?int $idimageObject;
	protected ?int $creativeWork;
	protected ?int $mediaObject;
	public ?string $caption = null;
	public ?int $representativeOfPage = null;

	public function __construct(array $value = ["@type" => "ImageObject"])
	{
		parent::__construct($value);

		$this->idimageObject = $value['idimageObject'] ?? null;
		$this->creativeWork = $value['creativeWork'] ?? null;
		$this->mediaObject = $value['mediaObject'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idimageObject;
	}
}
