<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\VideoObject;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\MediaObject;

class VideoObject extends MediaObject
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork','MediaObject'];

	private int $thing;
	protected int $idvideoObject;
	protected int $mediaObject;
	protected int $creativeWork;

	public function __construct(array $value = ["@type" => "VideoObject"])
	{
		parent::__construct($value);

		$this->idvideoObject = $value['idvideoObject'];
		$this->creativeWork = $value['creativeWork'];
		$this->mediaObject = $value['mediaObject'];
	}

	public function getId(): ?int
	{
		return $this->idvideoObject;
	}
}
