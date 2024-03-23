<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Person;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Person extends Entity
{
	/**
	 *
	 */
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
					if (stripos($properties,'homeLocation') !== false || stripos($properties,'address') !== false) {
						$dataPlace = ApiFactory::request()->type('place')->get(['idplace' => $value['homeLocation'],'properties'=>'address'])->ready();
						$value['homeLocation'] = isset($dataPlace[0]) ? ApiFactory::response()->type('place')->setData($dataPlace)->ready() : null;
					}
				}
				$returns[] = $value + $dataThing[0];
			}
		}
		return parent::array_sort($returns, $params);
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadedFiles
	 * @return string[]
	 */
  public function post(array $params = null, array $uploadedFiles = null): array
  {
	  $params['type'][] = 'Person';
		$dataPerson =  parent::createWithParent('thing', $params, $uploadedFiles);
		if (isset($dataPerson['id'])) {
			$idperson = $dataPerson['id'];
			$getPerson = ApiFactory::request()->type('person')->get(['idperson'=>$idperson])->ready();
			if (!empty($getPerson)) {
				return ApiFactory::response()->type('person')->setData($getPerson)->ready();
			} else {
				return ApiFactory::response()->message()->fail()->generic($getPerson);
			}
		} else {
			return ApiFactory::response()->message()->fail()->generic($dataPerson);
		}
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
