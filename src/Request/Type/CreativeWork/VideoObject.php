<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;

class VideoObject extends MediaObject
{
	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTable('videoObject');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('videoObject');
		$getData->setLeftJoin('creativeWork','creativeWork.idcreativeWork=videoObject.creativeWork');
		$getData->setLeftJoin('mediaObject','mediaObject.idmediaObject=videoObject.mediaObject');
		$getData->setParams($params);
		$data = $getData->render();
		if (isset($data[0]['idvideoObject'])) {
			foreach ($data as $key => $value) {
				$idthing = $value['thing'] ?? $value['idthing'] ?? null;
				// HAS PART
				if (in_array('hasPart', $properties)) {
					$dataHasPart = parent::getHasPart($idthing,'VideoObject', null, $params);
					if (isset($dataHasPart[0])) {
						$data[$key]['hasPart'] = ApiFactory::response()->type('videoObject')->setData($dataHasPart)->ready();
					}
				}
				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$dataIsPartOf = parent::getIsPartOf($idthing);
					if (isset($dataIsPartOf[0])) {
						$data[$key]['isPartOf'] = ApiFactory::response()->type('creativeWork')->setData($dataIsPartOf)->ready();
					}
				}
			}
		}
		return parent::sortData($data);
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadfiles
	 * @return array
	 * @throws Exception
	 */
	public function post(array $params = null, ?array $uploadfiles = null): array
	{
		$params['type'] = 'videoObject';
		return parent::post($params, $uploadfiles);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::update('mediaObject', $params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('mediaObject', $params);
	}
}
