<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class MediaObject extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	private ?int $thing;
	protected ?int $idmediaObject;
	protected ?int $creativeWork;
	public ?int $bitrate = null;
	public ?string $duration = null;
	public ?string $contentSize = null;
	public string $contentUrl;
	public ?string $encodingFormat = null;
	public ?int $height = null;
	public ?int $width = null;
	public string $uploadDate;

	public function __construct(array $value = ["@type" => "MediaObject"])
	{
		parent::__construct($value);

		$this->idmediaObject = $value['idmediaObject'] ?? null;
		$this->creativeWork = $value['creativeWork'] ?? null;

		$this->bitrate = $value['bitrate'] ?? null;
		$this->duration = $value['duration'] ?? null;
		$this->contentSize = $value['contentSize'] ?? null;
		$this->contentUrl = $value['contentUrl'];
		$this->encodingFormat = $value['encodingFormat'] ?? null;
		$this->height = $value['height'] ?? null;
		$this->width = $value['width'] ?? null;
		$this->uploadDate = $value['uploadDate'];
	}

	public function getId(): ?int
	{
		return $this->idmediaObject;
	}
}
