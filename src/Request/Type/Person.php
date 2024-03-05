<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Person extends Entity
{
  /**
   * @var array|string[]
   */
  protected array $properties = ["thing"];
  /**
   * @var array|string[]
   */
  protected array $hasTypes = ['thing'=>'Thing',"address" => 'PostalAddress', "contactPoint" => "ContactPoint", "image" => "ImageObject", "homeLocation"=>"Place"];

	public function __construct()
	{
		$this->setTable('person');
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
				if ($properties) {
					if (stripos($properties, 'contactPoint') !== false) {
						$dataContactPoint = ApiFactory::request()->type('contactPoint')->get(['thing' => $idthing])->ready();
						$value['contactPoint'] = isset($dataContactPoint[0]) ? ApiFactory::response()->type('contactPoint')->setData($dataContactPoint)->ready() : null;
					}
					if (stripos($properties,'imageObject') !== false || stripos($properties,'image') !== false) {
						$dataImageObject = ApiFactory::request()->type('imageObject')->get(['hasPart'=>$idthing])->ready();
						$value['image'] = isset($dataImageObject[0]) ? ApiFactory::response()->type('imageObject')->setData($dataImageObject)->ready() : null;
					}
					if (stripos($properties,'homeLocation') !== false || stripos($properties,'address') !== false) {
						$dataPlace = ApiFactory::request()->type('place')->get(['idplace' => $value['homeLocation'],'properties'=>'address'])->ready();
						$value['homeLocation'] = isset($dataPlace[0]) ? ApiFactory::response()->type('place')->setData($dataPlace)->ready() : null;
					}
				}
				$returns[] = $value + $dataThing[0];
			}
		}
		return $returns;
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadedFiles
	 * @return string[]
	 */
  public function post(array $params = null, array $uploadedFiles = null): array
  {
	  $idthing = $params['thing'] ?? null;
	  if (!$idthing) {
		  $params['type'] = 'person';
		  $dataThing = ApiFactory::request()->type('thing')->post($params, $uploadedFiles)->ready();
		  if (isset($dataThing['error'])) {
			  return ApiFactory::response()->message()->error()->anErrorHasOcurred($dataThing);
		  } else {
			  $idthing = $dataThing['id'];
		  }
	  }
	  return parent::post(['thing' => $idthing]);
  }

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		$idthing = $params['thing'] ?? null;
		$idperson = $params['idperson'];
		if (!$idthing) {
			$dataPerson = ApiFactory::request()->type('person')->get(['idperson'=>$idperson])->ready();
			if (isset($dataPerson[0])) {
				$value = $dataPerson[0];
				$idthing = $value['thing']['idthing'];
			} else {
				return $dataPerson;
			}
		}
		ApiFactory::request()->type('thing')->put(['idthing' => $idthing, 'dateModified' => date('Y-m-d H:i:s')])->ready();
		return parent::put($params);
	}
}
