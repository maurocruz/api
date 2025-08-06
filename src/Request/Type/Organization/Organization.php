<?php
namespace Plinct\Api\Request\Type\Organization;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Type\Thing;

class Organization extends Thing
{
	public function __construct()
	{
		parent::__construct();
		$this->setTable('organization');
	}

	/**
   * @param array $params
   * @return array
   */
  public function get(array $params = []): array
  {
	  $properties = self::propertiesToArray($params['properties'] ?? null);
		$makesOffer = $params['makesOffer'] ?? null;
	  $data = parent::getData($params);
		if (isset($data['error'])) {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		} elseif (!empty($data)) {
		  foreach ($data as $key => $value) {
			  $idthing = $value['thing'];
				$idorganization = $value['idorganization'];
			  $location = $value['location'] ?? null;
			  // PROPERTIES
			  if ($properties || $makesOffer) {
					// CONTACT POINT
				  if (in_array('contactPoint', $properties)) {
						$dataContactPoint = ApiFactory::request()->type('contactPoint')->get(['typeHasPart'=>'organization','idHasPart'=>$idthing])->ready();
						$data[$key]['contactPoint'] = isset($dataContactPoint[0]) ? ApiFactory::response()->type('contactPoint')->setData($dataContactPoint)->ready() : null;
				  }
					// IMAGE OBJECT
				  if (in_array('imageObject', $properties) || in_array('image', $properties)) {
					  $dataImageObject = ApiFactory::request()->type('imageObject')->get(['idHasPart'=>$idthing])->ready();
					  $data[$key]['image'] = isset($dataImageObject[0]) ? ApiFactory::response()->type('imageObject')->setData($dataImageObject)->ready() : null;
				  }
				  // LOCATION
				  if ($location && in_array('location', $properties)) {
					  $dataLocation = ApiFactory::request()->type('place')->get(['idplace' => $location])->ready();
					  $data[$key]['location'] = isset($dataLocation[0]) ? ApiFactory::response()->type('contactPoint')->setData($dataLocation[0])->ready() : null;
				  }
					// MEMBER
				  if (in_array('member', $properties)) {
						$dataMember = ApiFactory::request()->type('role')->get(['organization' => $idorganization,'properties' => 'member,memberOf'])->ready();
						if (isset($dataMember[0])) {
							$data[$key]['member'] = ApiFactory::response()->type('role')->setData($dataMember)->ready();
						}
				  }
					// OFFERS
					if (in_array('offer', $properties) || $makesOffer == 'offers') {
						$dataOffer = ApiFactory::request()->type('offer')->get(['offeredBy' => $idthing] + $params)->ready();
						$data[$key]['makesOffer'] = isset($dataOffer[0]) ? ApiFactory::response()->type('offer')->setData($dataOffer)->ready() : null;
					}
					// PRODUCTS
				  if (in_array('product', $properties) || $makesOffer == 'product') {
					  $dataOffer = ApiFactory::request()->type('offer')->get(['offeredBy' => $idthing,'type'=>'product'] + $params)->ready();
					  $data[$key]['makesOffer'] = isset($dataOffer[0]) ? ApiFactory::response()->type('offer')->setData($dataOffer)->ready() : null;
				  }
					// SERVICES
					if (in_array('service', $properties) || $makesOffer == 'service') {
						$dataOffer = ApiFactory::request()->type('offer')->get(['offeredBy' => $idthing,'type'=>'service'] + $params)->ready();
						$data[$key]['makesOffer'] = isset($dataOffer[0]) ? ApiFactory::response()->type('offer')->setData($dataOffer)->ready() : null;
					}
					// OFFER CATALOG
					if (in_array('hasOfferCatalog', $properties)) {
						unset($params['thing']);
						unset($params['idthing']);
						$dataOffer = ApiFactory::request()->type('offer')->get(['offeredBy' => $idthing] + $params)->ready();
						$data[$key]['hasOfferCatalog'] = isset($dataOffer[0]) ? ApiFactory::response()->type('offer')->setData($dataOffer)->setParams(['format'=>'ItemList'])->ready() : null;
						if ($value['hasOfferCatalog']) {
							$data[$key]['hasOfferCatalog']['@type'] = 'OfferCatalog';
						}
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
		return parent::createWithParent('thing',$params);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::update('thing',$params);
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
