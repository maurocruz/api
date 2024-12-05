<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Organization;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Organization extends Entity
{
	public function __construct()
	{
		$this->setTable('organization');
	}

	/**
   * @param array $params
   * @return array
   */
  public function get(array $params = []): array
  {
	  $returns = [];
	  $properties = $params['properties'] ?? '';
		$makesOffer = $params['makesOffer'] ?? null;
	  $data = parent::getData($params);
		if (isset($data['error'])) {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		} elseif (!empty($data)) {
		  foreach ($data as $value) {
			  $idthing = $value['thing'];
			  // PROPERTIES
			  if ($properties || $makesOffer) {
				  if (stripos($properties, 'contactPoint') !== false) {
					  $dataContactPoint = ApiFactory::request()->type('contactPoint')->get(['thing' => $idthing])->ready();
					  $value['contactPoint'] = isset($dataContactPoint[0]) ? ApiFactory::response()->type('contactPoint')->setData($dataContactPoint)->ready() : null;
				  }
				  if (stripos($properties,'imageObject') !== false || stripos($properties,'image') !== false) {
					  $dataImageObject = ApiFactory::request()->type('imageObject')->get(['isPartOf'=>$idthing])->ready();
					  $value['image'] = isset($dataImageObject[0]) ? ApiFactory::response()->type('imageObject')->setData($dataImageObject)->ready() : null;
				  }
				  if (stripos($properties,'location') !== false) {
					  $dataPlace = ApiFactory::request()->type('place')->get(['idplace' => $value['location'],'properties'=>'address'])->ready();
					  $value['location'] = isset($dataPlace[0]) ? ApiFactory::response()->type('place')->setData($dataPlace)->ready() : null;
				  }
					if (stripos($properties,'offer') !== false || $makesOffer == 'offers') {
						$dataOffer = ApiFactory::request()->type('offer')->get(['offeredBy' => $idthing] + $params)->ready();
						$value['makesOffer'] = isset($dataOffer[0]) ? ApiFactory::response()->type('offer')->setData($dataOffer)->ready() : null;
					}
				  if (stripos($properties,'product') !== false || $makesOffer == 'product') {
					  $dataOffer = ApiFactory::request()->type('offer')->get(['offeredBy' => $idthing,'type'=>'product'] + $params)->ready();
					  $value['makesOffer'] = isset($dataOffer[0]) ? ApiFactory::response()->type('offer')->setData($dataOffer)->ready() : null;
				  }
					if (stripos($properties,'service') !== false || $makesOffer == 'service') {
						$dataOffer = ApiFactory::request()->type('offer')->get(['offeredBy' => $idthing,'type'=>'service'] + $params)->ready();
						$value['makesOffer'] = isset($dataOffer[0]) ? ApiFactory::response()->type('offer')->setData($dataOffer)->ready() : null;
					}
			  }
			  $returns[] = $value;
		  }
	  }
	  return parent::sortData($returns);
  }

	public function post(array $params = null): array
	{
		return parent::createWithParent('thing',$params);
	}

	public function put(array $params = null): array
	{
		return parent::update('thing',$params);
	}

	public function delete(array $params): array
	{
		return parent::erase('thing',$params);
	}
}
