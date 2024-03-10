<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Event;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Event extends Entity
{
	public function __construct()
	{
		$this->setTable('event');
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
				// PROPERTIES
				if ($properties) {
					// image
					if (stripos($properties,'imageObject') !== false || stripos($properties,'image') !== false) {
						$dataImageObject = ApiFactory::request()->type('imageObject')->get(['hasPart'=>$idthing])->ready();
						$value['image'] = isset($dataImageObject[0]) ? ApiFactory::response()->type('imageObject')->setData($dataImageObject)->ready() : null;
					}
					// location
					if (stripos($properties,'location') !== false) {
						$dataPlace = ApiFactory::request()->type('place')->get(['idplace' => $value['location'],'properties'=>'address'])->ready();
						$value['location'] = isset($dataPlace[0]) ? ApiFactory::response()->type('place')->setData($dataPlace)->ready() : null;
					}
				}
				$returns[] = $value + $dataThing[0];
			}
		}
		return parent::array_sort($returns, $params);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
  public function post(array $params = null): array
  {
	  $params['type'] = 'Event';
		return parent::create('thing', $params);
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
