<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Place;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Place extends Entity
{

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
		return $returns;
	}

	/**
	 * @param array|null $params
	 * @return string[]
	 */
	public function post(array $params = null): array
	{
		$idthing = $params['thing'] ?? null;
		if (!$idthing) {
			$params['type'] = 'place';
			$dataThing = ApiFactory::request()->type('thing')->post($params)->ready();
			if (isset($dataThing['error'])) {
				return ApiFactory::response()->message()->error()->anErrorHasOcurred($dataThing);
			} else {
				$idthing = $dataThing['id'];
			}
		}
		return parent::post(['thing' => $idthing]);
	}
}
