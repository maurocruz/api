<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Type\Thing;

class Service extends Thing
{
	/**
	 *
	 */
  public function __construct()
  {
		parent::__construct();
		$this->setTable('service');
  }

	/**
  * @param array $params
  * @return array
  */
	public function get(array $params = []): array
	{
		$properties = parent::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('service');
		$getData->setParams($params);
		$data = $getData->render();
		if ($properties) {
			foreach ($data as $key => $value) {
				$idthing = $value['idthing'];
				if (in_array('provider', $properties)) {
					$provider = $value['provider'];
					$dataProvider = ApiFactory::request()->type('thing')->get(['idthing'=>$provider, 'hasPart'=>true])->ready();
					if (isset($dataProvider[0])) {
						$data[$key]['provider'] = ApiFactory::response()->type($dataProvider[0]['type'])->setData($dataProvider[0])->ready();
					}
				}
				if (in_array('offer', $properties)) {
					$dataOffer = ApiFactory::request()->type('offer')->get(['itemOffered'=>$idthing,'orderBy'=>'validThrough desc, InStock'])->ready();
					$data[$key]['offers'] = ApiFactory::response()->type('offer')->setData($dataOffer)->ready();
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
		$idservice = $params['idservice'] ?? null;
		if ($idservice) {
			return parent::delete($params);
		}
		return false;
	}
}
