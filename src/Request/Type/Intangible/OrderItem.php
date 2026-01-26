<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\Relationship;

class OrderItem extends Entity
{
	/**
	 *
	 */
  public function __construct(Relationship $relationship = null)
  {
		parent::__construct($relationship);
		$this->setTable('orderItem');
  }

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('orderItem');
		if (in_array('offer', $properties)) {
			$getData->setLeftJoin('offer', '`offer`.idoffer=`orderItem`.offer');
		}
		$getData->setParams($params);
		$data = $getData->render();

		foreach ($data as $key=>$value) {
			if ($properties) {
				// ORDERED iTEM
				if(in_array('orderedItem', $properties)) {
					$orderedItem = $value['orderedItem'];
					$paramsOrderedItems = ['idthing'=>$orderedItem,'hasPart'=>true];
					if (isset($params['idservice'])) {
						$paramsOrderedItems = $paramsOrderedItems + ['idservice'=>$params['idservice']];
					}
					$dataThing = ApiFactory::request()->type('thing')->get($paramsOrderedItems)->ready();
					if (isset($dataThing[0])) {
						$type = $dataThing[0]['type'];
						$data[$key]['orderedItem'] = ApiFactory::response()->type($type)->setData($dataThing[0])->ready();
					} elseif (isset($params['idservice'])) {
						unset($data[$key]);
					}
				}
				// OFFER
				if (in_array('offer',$properties)) {
					$dataOffer = [
						'@type' => "Offer",
						'idoffer' => $value['idoffer'],
						'itemOffered' => $value['itemOffered'],
						'offeredBy' => $value['offeredBy'] ?? null,
						'price' => $value['price'],
						'priceCurrency' => $value['priceCurrency'],
						'validThrough' => $value['validThrough'],
						'availability' => $value['availability'],
						'eligibleQuantity' => $value['eligibleQuantity'],
						'eligibleDuration' => $value['eligibleDuration']
					];
					$data[$key]['offer'] = ApiFactory::response()->type('offer')->setData($dataOffer)->ready();
					unset($data[$key]['idoffer']);
					unset($data[$key]['itemOffered']);
					unset($data[$key]['offeredBy']);
					unset($data[$key]['price']);
					unset($data[$key]['priceCurrency']);
					unset($data[$key]['validThrough']);
					unset($data[$key]['availability']);
					unset($data[$key]['eligibleQuantity']);
					unset($data[$key]['eligibleDuration']);
				} else {
					unset($data[$key]['offer']);
				}
			}
		}
		return parent::sortData($data);
	}

	/**
	 * @param null $params
	 * @param array|null $uploadfiles
	 * @return array
   */
  public function post($params = null, array $uploadfiles = null): array
  {
		$orderedItem = $params['orderedItem'] ?? null;
		$orderItemNumber  = $params['orderItemNumber'] ?? null;
		if ($orderedItem && $orderItemNumber) {
			return parent::post($params);
		}
	  return ApiFactory::response()->message()->fail()->inputDataIsMissing();
  }
}
