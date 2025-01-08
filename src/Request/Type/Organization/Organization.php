<?php
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
		$propertiesArray = explode(',',$properties);
		array_walk($propertiesArray, function (&$value) {$value = trim($value);});
		$makesOffer = $params['makesOffer'] ?? null;
	  $data = parent::getData($params);
		if (isset($data['error'])) {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		} elseif (!empty($data)) {
		  foreach ($data as $value) {
			  $idthing = $value['thing'];
			  // PROPERTIES
			  if ($properties || $makesOffer) {
					// LOCATION
				  if (in_array('location', $propertiesArray)) {
						$location = $value['location'];
					  $dataLocation = ApiFactory::request()->type('place')->get(['idplace' => $location])->ready();
						$value['location'] = isset($dataLocation[0]) ? ApiFactory::response()->type('contactPoint')->setData($dataLocation[0])->ready() : null;
				  }
					// IMAGE OBJECT
				  if (in_array('imageObject', $propertiesArray) || in_array('image', $propertiesArray)) {
					  $dataImageObject = ApiFactory::request()->type('imageObject')->get(['isPartOf'=>$idthing])->ready();
					  $value['image'] = isset($dataImageObject[0]) ? ApiFactory::response()->type('imageObject')->setData($dataImageObject)->ready() : null;
				  }
					// OFFERS
					if (in_array('offer', $propertiesArray) || $makesOffer == 'offers') {
						$dataOffer = ApiFactory::request()->type('offer')->get(['offeredBy' => $idthing] + $params)->ready();
						$value['makesOffer'] = isset($dataOffer[0]) ? ApiFactory::response()->type('offer')->setData($dataOffer)->ready() : null;
					}
					// PRODUCTS
				  if (in_array('product', $propertiesArray) || $makesOffer == 'product') {
					  $dataOffer = ApiFactory::request()->type('offer')->get(['offeredBy' => $idthing,'type'=>'product'] + $params)->ready();
					  $value['makesOffer'] = isset($dataOffer[0]) ? ApiFactory::response()->type('offer')->setData($dataOffer)->ready() : null;
				  }
					// SERVICES
					if (in_array('service', $propertiesArray) || $makesOffer == 'service') {
						$dataOffer = ApiFactory::request()->type('offer')->get(['offeredBy' => $idthing,'type'=>'service'] + $params)->ready();
						$value['makesOffer'] = isset($dataOffer[0]) ? ApiFactory::response()->type('offer')->setData($dataOffer)->ready() : null;
					}
					// OFFER CATALOG
					if (in_array('hasOfferCatalog', $propertiesArray)) {
						unset($params['thing']);
						unset($params['idthing']);
						$dataOffer = ApiFactory::request()->type('offer')->get(['offeredBy' => $idthing] + $params)->ready();
						$value['hasOfferCatalog'] = isset($dataOffer[0]) ? ApiFactory::response()->type('offer')->setData($dataOffer)->setParams(['format'=>'ItemList'])->ready() : null;
						if ($value['hasOfferCatalog']) {
							$value['hasOfferCatalog']['@type'] = 'OfferCatalog';
						}
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
