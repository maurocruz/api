<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class OrderItem extends Entity
{
  public function __construct()
  {
		$this->setTable('orderItem');
  }

	public function get(array $params = []): array
	{
		$properties = $params['properties'] ?? '';
		$offer = stripos($properties, 'offer') !== false;
		$join = $offer !== false ? "LEFT JOIN `offer` ON `offer`.idoffer=`orderItem`.offer" : null;
		$data = parent::getData($params, $join);
		foreach ($data as $key=>$value) {
			if (!empty($properties)) {
				// ORDERED iTEM
				if(stripos($properties, 'orderedItem') !== false) {
					$orderedItem = $value['orderedItem'];
					$dataThing = ApiFactory::request()->type('thing')->get(['idthing'=>$orderedItem,'hasPart'=>true])->ready();
					if (isset($dataThing[0])) {
						$type = $dataThing[0]['type'];
						$data[$key]['orderedItem'] = ApiFactory::response()->type($type)->setData($dataThing[0])->ready();
					}
				}
				// OFFER
				if ($offer) {
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
				}
			}
		}
		return parent::sortData($data);
	}

	/**
   * @param ?array $params
   * @return array
   */
  public function post($params = null): array
  {
		$multiDimensional = $params['multidimensional'] ?? false;
		if ($multiDimensional) {
			$params = json_decode($params['multidimensional'], true);
		}
		$items = $params['items'] ?? null;
		if ($items) {
			$returns = [];
			foreach ($items as $item) {
				$referencesOrder = $item['referencesOrder'] ?? null;
				$orderedItem = $item['orderedItem'] ?? null;
				$offer = $item['offer'] ?? null;
				$orderQuantity = $item['orderQuantity'] ?? null;
				if ($referencesOrder && $orderedItem && $offer && $orderQuantity) {
					$dataPost = parent::post($item);
					if (array_key_exists('status',$dataPost) && $dataPost['status'] == 'success') {
						$returns['status'] = "success";
						$returns['code'] = '0000';
						$returns['message'] = 'Successfully created';
						$returns['data'][] = $dataPost['data'][0];
					}
				}
			}
			return $returns;
		}
		return ApiFactory::response()->message()->fail()->inputDataIsMissing();
  }
}
