<?php
namespace Plinct\Api\Request\Type\Event;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Type\Thing;

class Event extends Thing
{
	public function __construct()
	{
		parent::__construct();
		$this->setTable('event');
	}

	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('event');
		$getData->setParams($params);
		$data = $getData->render();
		// PROPERTIES
		if (!empty($data) && $properties) {
			foreach ($data as $key => $value) {
				$idevent = $value['idevent'];
				$idthing = $value['thing'];
				$location = $value['location'];
				// image object
				if (in_array('imageObject', $properties) || in_array('image', $properties)) {
					$dataImageObject = ApiFactory::request()->type('imageObject')->get(['idHasPart' => $idthing])->ready();
					$data[$key]['image'] = isset($dataImageObject[0]) ? ApiFactory::response()->type('imageObject')->setData($dataImageObject)->ready() : null;
				}
				// location
				if (in_array('location',$properties)) {
					$dataLocation = ApiFactory::request()->type('place')->get(['idplace'=>$location, 'properties'=>'geo'])->ready();
					if(isset($dataLocation[0])){
						$data[$key]['location'] = ApiFactory::response()->type('place')->setData($dataLocation[0])->ready();
					}
				}
				// subEvent
				if (in_array('subEvent', $properties)) {
					$dataSubEvent = ApiFactory::request()->type('event')->get(['superEvent'=>$idevent])->ready();
					if (isset($dataSubEvent[0])) {
						$data[$key]['subEvent'] = ApiFactory::response()->type('event')->setData($dataSubEvent)->ready();
					}
				}
				// super event
				if (in_array('superEvent', $properties)) {
					$idSuperEvent = $value['superEvent'] ?? null;
					if ($idSuperEvent) {
						$dataSuperEvent = $this->get(['idevent' => $idSuperEvent]);
						$data[$key]['superEvent'] = $dataSuperEvent[0] ? ApiFactory::response()->type('event')->setData($dataSuperEvent[0])->ready() : null;
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
	 */
  public function post(array $params = null, array $uploadfiles = null): array
  {
		$name = $params['name'] ?? null;
		$startDate = $params['startDate'] ?? null;
		$endDate = $params['endDate'] ?? null;
		$location = $params['location'] ?? null;
		if ($name && $startDate && $endDate && $location) {
			if (isset($params['superEvent']) && $params['superEvent'] == '') {
				$params['superEvent'] = null;
			}
			if (isset($params['organizer']) && $params['organizer'] == '') {
				$params['organizer'] = null;
			}
			return parent::createWithParent('thing',$params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: name, startDate, endDate, location']);
		}
  }

	/**
	 * @param array|null $params
	 * @return array
	 */
  public function put(array $params = null): array
  {
		$superEvent = $params['superEvent'] ?? null;
		$organizer = $params['organizer'] ?? null;
		if (is_string($superEvent)) {
			unset($params['superEvent']);
		}
		if (is_string($organizer)) {
			unset($params['organizer']);
		}
    return parent::update('thing', $params);
  }

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('thing',$params);
	}
}
