<?php
namespace Plinct\Api\Request\Type;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Thing extends Entity
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
		if (array_key_exists('where', $params)) {
			$params['where'] = urldecode($params['where']);
		}
		$data = parent::getData($params);
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				$idthing = $value['idthing'] ?? null;
				$type = $value['type'] ?? null;
				if ($hasPart && $idthing && $type) {
					$dataHasPart = ApiFactory::request()->type(lcfirst($type))->get(['thing' => $idthing] + $params)->ready();
					if (isset($dataHasPart[0])) {
						$data[$key] = $dataHasPart[0] + $value;
					} elseif (isset($params["id".lcfirst($type)])) {
						unset($data[$key]);
					}
				}
				if ($properties && $idthing) {
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
	 * @param array $params
	 * @param array|null $uploadfiles
	 * @return array
	 * @throws Exception
	 */
	public function post(array $params, array $uploadfiles = null): array
	{
		$name = $params['name'] ?? null;
		$type = $params['type'] ?? "Thing";
		$params['dateCreated'] = $params['dateCreated'] ?? date('Y-m-d H:i:s');
		$params['dateModified'] = $params['dateModified'] ?? date('Y-m-d H:i:s');
		if ($uploadfiles) {
			return parent::uploadfiles($params, $uploadfiles);
		} elseif ($name && $type) {
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
			$dataThing = parent::getData($params);
			if (isset($dataThing[0])) {
				$returns = parent::delete(['idthing'=>$idthing]);
				return ApiFactory::response()->message()->success('Thing was deleted', $returns);
			} else {
				return ApiFactory::response()->message()->fail()->generic(['Thing not found: '.$idthing]);
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idthing or thing"]);
		}
	}
}
