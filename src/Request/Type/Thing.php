<?php
namespace Plinct\Api\Request\Type;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\HttpRequestInterface;

class Thing extends Entity implements HttpRequestInterface
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('thing');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$hasPart = array_key_exists('hasPart', $params);
		$data = parent::getData($params);
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				$idthing = $value['idthing'];
				$type = $value['type'];
				if ($hasPart) {
					$dataHasPart = ApiFactory::request()->type(lcfirst($type))->get(['thing' => $idthing] + $params)->ready();
					if (isset($dataHasPart[0])) {
						$data[$key] = $dataHasPart[0] + $value;
					} elseif (isset($params["id".lcfirst($type)])) {
						unset($data[$key]);
					}
				}
				if ($properties) {
					// IMAGE OBJECT
					if (in_array('image',$properties)) {
						$data[$key]['image'] = parent::getProperties('imageObject', ['idHasPart' => $idthing, 'orderBy' => 'position']);
					}
				}
			}
		}
		return parent::sortData($data);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$name = $params['name'] ?? null;
		$type = $params['type'] ?? null;
		$params['dateCreated'] = date('Y-m-d H:i:s');
		if ($name && $type) {
			return parent::post($params);
		}
		return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: name and type']);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		$idthing = $params['idthing'] ?? null;
		$params['dateModified'] = date('Y-m-d H:i:s');
		if ($idthing) {
			return parent::put($params);
		}
		return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idthing"]);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		$idthing = $params['idthing'] ?? $params['thing'] ?? null;
		if ($idthing) {
			return parent::delete(['idthing'=>$idthing]);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idcreativeWork or creativeWork"]);
		}
	}
}
