<?php
declare(strict_types=1);
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
		$returns = [];
		$properties = $params['properties'] ?? null;
		$dataThing = parent::getData($params);

		if (!empty($dataThing) && $properties !== null) {
			foreach ($dataThing as $key => $value) {
				$idthing = $value['idthing'];
				if (strpos($properties, 'image') !== false) $value['image'] = parent::getProperties('imageObject', ['isPartOf' => $idthing, 'orderBy' => 'position']);
				$returns[$key] = $value;
			}
		} else {
			$returns = $dataThing;
		}
		return parent::sortData($returns);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$name = $params['name'] ?? null;
		$type = $params['type'] ?? null;
		if ($name && $type) {
			$params['type'] = current($params['type']);
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
