<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Event;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
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
		$name = $params['name'] ?? null;
		$startDate = $params['startDate'] ?? null;
		if ($name) {
			$dataThing = ApiFactory::request()->type('thing')->get($params)->ready();
			if (empty($dataThing)) {
				return ApiFactory::response()->message()->fail()->generic();
			} else {
					foreach ($dataThing as $thing) {
						$idthing = $thing['idthing'];
						if ($startDate && strlen($startDate) === 10) {
							unset($params['startDate']);
							$params['startDateLike'] = $startDate;
						}
						$dataEvent = parent::getData(['thing'=>$idthing] + $params);
						if (!empty($dataEvent)) {
							$value = $dataEvent[0];
							$returns[] = $thing + self::getValuesProperties($properties, $value, $idthing);
						}
					}
			}
		} else {
			$data = parent::getData($params);
			if (!empty($data)) {
				foreach ($data as $value) {
					$idthing = $value['thing'];
					$dataThing = ApiFactory::request()->type('thing')->get(['idthing' => $idthing])->ready();
					// PROPERTIES
					$returns[] = self::getValuesProperties($properties, $value, $idthing) + $dataThing[0];
				}
			}
		}
		return parent::sortData($returns);
	}

	private function getValuesProperties(string $properties, array $value, int $idthing): ?array
	{
		if ($properties) {
			// image
			if (stripos($properties, 'imageObject') !== false || stripos($properties, 'image') !== false) {
				$dataImageObject = ApiFactory::request()->type('imageObject')->get(['hasPart' => $idthing])->ready();
				$value['image'] = isset($dataImageObject[0]) ? ApiFactory::response()->type('imageObject')->setData($dataImageObject)->ready() : null;
			}
			// location
			if (stripos($properties, 'location') !== false) {
				$dataPlace = ApiFactory::request()->type('place')->get(['idplace' => $value['location'], 'properties' => 'address'])->ready();
				$value['location'] = isset($dataPlace[0]) ? ApiFactory::response()->type('place')->setData($dataPlace)->ready()[0] : null;
			}
			// subEvent
			if (stripos($properties, 'subEvent') !== false) {
				$query = "SELECT * FROM thing_has_thing where idHasPart=$idthing AND typeHasPart='Event';";
				$dataIsPartOf = PDOConnect::run($query);
				foreach ($dataIsPartOf as $valueSubEvent) {
					$value['subEvent'][] = ['idevent' => $valueSubEvent['idIsPartOf']];
				}
			}
		}
		return $value;
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
  public function post(array $params = null): array
  {
	  $params['type'][] = 'Event';
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
