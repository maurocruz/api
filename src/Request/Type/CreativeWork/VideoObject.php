<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;

class VideoObject extends Entity
{
	public function __construct()
	{
		$this->setTable('videoObject');
	}

	public function get(array $params = []): array
	{
		$getData = new GetData('videoObject');
		$getData->setLeftJoin('creativeWork','creativeWork.idcreativeWork=videoObject.creativeWork');
		$getData->setLeftJoin('mediaObject','mediaObject.idmediaObject=videoObject.mediaObject');
		$getData->setParams($params);
		$data = $getData->render();
		return parent::sortData($data);
	}
}
