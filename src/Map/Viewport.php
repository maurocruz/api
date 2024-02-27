<?php

declare(strict_types=1);

namespace Plinct\Api\Map;

use Plinct\Api\Request\Server\GetData\GetData;

class Viewport // DEPRECATED
{
	private ?array $params;

	public function setParams(array $params = null): Viewport
	{
		unset($params['token']);
		$this->params = $params;
		return $this;
	}

	public function ready(): array
	{
		$returnData = null;

		if($this->params) {
			$getData = new GetData('map_viewport');
			$getData->setParams($this->params);
			$data = $getData->render();
			foreach ($data as $value) {
				$value['viewport'] = json_decode($value['viewport'], true);
				$returnData[] = $value;
			}
			return ['status'=>'success','data'=>$returnData];
		}
		return ['status'=>'fail','message'=>'Viewport() return empty'];
	}
}