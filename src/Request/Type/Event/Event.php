<?php
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
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$data = parent::getData($params);
		// PROPERTIES
		if (!empty($data) && $properties) {
			foreach ($data as $key => $value) {
				$idthing = $value['thing'];
				if (in_array('imageObject', $properties) || in_array('image', $properties)) {
					$dataImageObject = ApiFactory::request()->type('imageObject')->get(['hasPart' => $idthing])->ready();
					$data[$key]['image'] = isset($dataImageObject[0]) ? ApiFactory::response()->type('imageObject')->setData($dataImageObject)->ready() : null;
				}
				// location
				if (in_array('location',$properties)) {
					$dataPlace = ApiFactory::request()->type('place')->get(['idplace' => $value['location'], 'properties' => 'address'])->ready();
					$data[$key]['location'] = isset($dataPlace[0]) ? ApiFactory::response()->type('place')->setData($dataPlace)->ready()[0] : null;
				}
				// subEvent
				if (in_array('subEvent', $properties)) {
					$query = "SELECT * FROM thing_has_thing where idHasPart=$idthing AND typeHasPart='Event';";
					$dataIsPartOf = PDOConnect::run($query);
					foreach ($dataIsPartOf as $valueSubEvent) {
						$data[$key]['subEvent'][] = ['idevent' => $valueSubEvent['idIsPartOf']];
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
