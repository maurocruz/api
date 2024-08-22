<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\Request\Server\Entity;

class VideoObject extends Entity
{
	public function __construct()
	{
		$this->setTable('videoObject');
	}

	public function get(array $params = []): array
	{
		$data = parent::getData($params);
		if (!empty($data)) {
			foreach ($data as $key => $item) {
				$idmediaObject = $item['mediaObject'];
				$dataMediaObject = (new MediaObject())->getMediaObjectData(['idmediaObject'=>$idmediaObject] + $params);
				$data[$key] = $item + $dataMediaObject[0];
			}
		}
		return parent::sortData($data);
	}
}
