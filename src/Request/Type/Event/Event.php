<?php
namespace Plinct\Api\Request\Type\Event;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\Relationship;

class Event extends Entity
{
	public function __construct()
	{
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
					$dataImageObject = ApiFactory::request()->type('imageObject')->get(['hasPart' => $idthing])->ready();
					$data[$key]['image'] = isset($dataImageObject[0]) ? ApiFactory::response()->type('imageObject')->setData($dataImageObject)->ready() : null;
				}
				// location
				if (in_array('location',$properties)) {
					$dataLocation = ApiFactory::request()->type('place')->get(['idplace'=>$location])->ready();
					if(isset($dataLocation[0])){
						$data[$key]['location'] = ApiFactory::response()->type('place')->setData($dataLocation[0])->ready();
					}
				}
				// subEvent
				if (in_array('subEvent', $properties)) {
					$dataSubEvent = ApiFactory::request()->type('event')->get(['superEvent'=>$idevent])->ready();
					$data[$key]['subEvent'] = ApiFactory::response()->type('event')->setData($dataSubEvent)->ready();
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
	 * @return array
	 */
  public function post(array $params = null): array
  {
		$name = $params['name'] ?? null;
		$type = $params['type'] ?? null;
		$description = $params['description'] ?? null;
		$startDate = $params['startDate'] ?? null;
		$endDate = $params['endDate'] ?? null;
		$typeHasPart = $params['typeHasPart'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		$typeIsPartOf = $params['typeIsPartOf'] ?? null;
		$idIsPartOf = $params['idIsPartOf'] ?? null;
		if ($name && $type && $description && $startDate && $endDate) {
			return parent::createWithParent('thing', $params);
		} elseif ($typeHasPart && $idHasPart && $typeIsPartOf && $idIsPartOf) {
			$dataEventHasPart = self::get(['thing' => $idHasPart]);
			$dataEventIsPartOf = self::get(['thing' => $idIsPartOf]);
			if (isset($dataEventIsPartOf[0]) && $dataEventIsPartOf[0]) {
				$ideventHasPart = $dataEventHasPart[0]['idevent'];
				$ideventIsPartOf = $dataEventIsPartOf[0]['idevent'];
				$postRelationship = parent::createRelationShip($idHasPart, $typeHasPart, $idIsPartOf, $typeIsPartOf);
				if(empty($postRelationship)) {
					return self::put(['idevent'=>$ideventIsPartOf,'superEvent'=>$ideventHasPart]);
				} else {
					return ApiFactory::response()->message()->fail()->generic($postRelationship);
				}
			} else {
				return ApiFactory::response()->message()->fail()->generic([$dataEventIsPartOf,$dataEventHasPart],"Event is part of is missing");
			}
		} else {
			return ApiFactory::response()->message()->fail()->generic([],"Mandatory Fields Are Missing");
		}
  }

	/**
	 * @param array|null $params
	 * @return array
	 */
  public function put(array $params = null): array
  {
		if (array_key_exists('superEvent', $params)) {
			$params['superEvent'] = !!$params['superEvent'] ? $params['superEvent'] : null;
		}
		if (array_key_exists('location', $params)) {
			$params['location'] = !!$params['location'] ? $params['location'] : null;
		}
    return parent::update('thing', $params);
  }

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		$typeHasPart = $params['typeHasPart'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		$typeIsPartOf = $params['typeIsPartOf'] ?? null;
		$idIsPartOf = $params['idIsPartOf'] ?? null;
		// delete super event
		if ($typeHasPart && $idHasPart && $typeIsPartOf && $idIsPartOf) {
			$success = true;
			$returns=[];
			// delete relationship
			$dataRel = (new Relationship($typeHasPart, $idHasPart, $typeIsPartOf, $idIsPartOf))->delete();
			if ($dataRel['rows'] == 0) {
				$returns[] = ApiFactory::response()->message()->fail()->generic($dataRel,"Relationship Not Found");
				$success = false;
			} else {
				$returns[] = ApiFactory::response()->message()->success("Relationship Deleted",$dataRel);
			}
			// delete super event
			$getEvent = self::get(['thing' => $idIsPartOf]);
			if (isset($getEvent[0])) {
				$idevent = $getEvent[0]['idevent'];
				$putEvent = self::put(['idevent'=>$idevent,'superEvent'=>null]);
				if(isset($putEvent['status']) && $putEvent['status'] == 'success') {
					$returns[] = ApiFactory::response()->message()->success("Super event excluded in event",$putEvent);
				} else {
					$returns[] = ApiFactory::response()->message()->fail()->generic($putEvent);
					$success = false;
				}
			} else {
				$returns[] = ApiFactory::response()->message()->fail()->generic($getEvent);
				$success = false;
			}
			return $success ? ApiFactory::response()->message()->success("Relationships deleted",$returns) : ApiFactory::response()->message()->fail()->generic($returns);
		} else {
			return parent::erase('thing', $params);
		}
	}
}
