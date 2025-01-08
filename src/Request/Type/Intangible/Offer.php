<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Offer extends Entity
{
	/**
	 *
	 */
  public function __construct()
  {
		$this->setTable('offer');
  }

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$isValidThrough = array_key_exists('isValidThrough', $params);
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$where = null;
		if ($isValidThrough) {
			$where = "`validThrough` >= CURDATE()";
		}
		$data = parent::getData($params, null, $where);
		if (isset($data['error'])) {
			return $data;
		}
		if ($properties) {
			foreach ($data as $key => $value) {
				//  itemOffered
				if ($properties && in_array('itemOffered', $properties)) {
					$itemOfferd = $value['itemOffered'];
					$dataItemOffered = ApiFactory::request()->type('thing')->get(['idthing' => $itemOfferd, 'hasPart'=>true])->ready();
					if (isset($dataItemOffered[0])) {
						$typeItemOffered = $dataItemOffered[0]['type'];
						$data[$key]['itemOffered'] = ApiFactory::response()->type($typeItemOffered)->setData($dataItemOffered[0])->ready();
					}
				}
				// offeredBy
				if ($properties && in_array('offeredBy', $properties)) {
					$offeredBy = $value['offeredBy'];
					$dataOfferedBy = ApiFactory::request()->type('thing')->get(['idthing' => $offeredBy, 'hasPart'=>true])->ready();
					if (isset($dataOfferedBy[0])) {
						$typeOfferedBy = $dataOfferedBy[0]['type'];
						$data[$key]['offeredBy'] = ApiFactory::response()->type($typeOfferedBy)->setData($dataOfferedBy[0])->ready();
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
	public function post(?array $params = null): array
	{
		$params['dateCreated'] = date("Y-m-d H:i:s");
		return parent::post($params);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::update('thing', $params);
	}
}
