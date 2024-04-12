<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Organization;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Type\Offer;

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
	  $properties = $params['properties'] ?? null;
	  $data = parent::getData($params);
	  if (!empty($data)) {
		  foreach ($data as $value) {
			  $idthing = $value['thing'];
			  $dataThing = ApiFactory::request()->type('thing')->get(['idthing' => $idthing])->ready();
			  // PROPERTIES
			  if ($properties) {
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
			  }
			  $returns[] = $value + $dataThing[0];
		  }
	  }
	  return parent::array_sort($returns, $params);
  }

	public function post(array $params = null): array
	{
		$params['type'][] = 'Organization';
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
