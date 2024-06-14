<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Place;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Place extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('place');
	}

	public function get(array $params = []): array
	{
		$returns = [];
		$properties = $params['properties'] ?? null;
		$data = parent::getData($params);
		if (!empty($data)) {
			foreach ($data as $value) {
				$idthing = $value['thing'];
				$dataThing = ApiFactory::request()->type('thing')->get(['idthing' => $idthing])->ready();
				if ($properties) {
					if (stripos($properties, 'address') !== false) {
						$dataAddress = ApiFactory::request()->type('postalAddress')->get(['thing'=>$idthing])->ready();
						$value['address'] = !empty($dataAddress) ? ApiFactory::response()->type('postalAddress')->setData($dataAddress)->ready() : null;
					}
				}
				$returns[] = $value + $dataThing[0];
			}
		}
		return parent::sortData($returns);
	}

	/**
	 * @param array|null $params
	 * @return string[]
	 */
	public function post(array $params = null): array
	{
		$params['type'][] = 'Place';
		return parent::createWithParent('thing', $params);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::update('thing', $params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('thing', $params);
	}
}
