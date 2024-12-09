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
		$this->setProperties(['contactPoint']);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = $params['properties'] ?? null;
		$hasPart = array_key_exists('hasPart', $params);
		$dataThing = parent::getData($params);
		if (!empty($dataThing)) {
			foreach ($dataThing as $key => $value) {
				$idthing = $value['idthing'];
				$type = $value['type'];
				if ($hasPart) {
					$dataHasPart = ApiFactory::request()->type(lcfirst($type))->get(['thing' => $idthing])->ready();
				}
				if ($properties) {
					if (str_contains($properties, 'image')) $value['image'] = parent::getProperties('imageObject', ['isPartOf' => $idthing, 'orderBy' => 'position']);
				}
				$dataThing[$key] = isset($dataHasPart[0]) ? $dataHasPart[0] + $value : $value;
			}
		}
		return parent::sortData($dataThing);
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
