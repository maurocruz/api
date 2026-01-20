<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;

class Order extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('order');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = parent::propertiesToArray($params['properties'] ?? null);
		$customerNameLike = $params['customerNameLike'] ?? $params['nameLike'] ?? null;
		$getData = new GetData('order');
		$getData->setParams($params);
		if ($customerNameLike !== null) {
			$getData->setParams(['nameLike'=>$customerNameLike]);
			$getData->setLeftJoin('thing','`thing`.idthing=order.customer');
			$getData->setWhere("`thing`.`name` LIKE '%$customerNameLike%'");
			$properties[] = 'customer';
		} elseif(in_array('customer', $properties)) {
			$getData->setLeftJoin('thing','`thing`.idthing=order.customer', 'ct');
		}
		$data = $getData->render();
		// ORDER STATUS
		if (isset($data['error'])) { // ERROR
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		} elseif (!empty($data)) {
			foreach ($data as $key => $value) {
				$tags = $value['tags'] ?? null;
				if ($tags !== null) {
					// tags
					$data[$key]['identifier'][] = [
						'@type' => 'PropertyValue',
						'name' => 'tags',
						'value' => $value['tags']
					];
					unset($data[$key]['tags']);
				}
				// properties
				if (!!$properties) {
					$idorder = $value['idorder'];
					// ACCEPTED OFFER
					if (in_array('acceptedOffer', $properties)) {
						$acceptedOffer = null;
						$dataOrderItem = ApiFactory::request()->type('orderItem')->get(['orderItemNumber'=>$idorder,'properties'=>'offer,orderedItem'])->ready();
						if (isset($dataOrderItem[0])) {
							foreach ($dataOrderItem as $kOI => $orderItem) {
								$acceptedOffer[] = $orderItem['offer'];
								unset($dataOrderItem[$kOI]['offer']);
							}
							if (in_array('orderedItem', $properties)) {
								$data[$key]['orderedItem'] = ApiFactory::response()->type('OrderItem')->setData($dataOrderItem)->ready();
							}
						}
						$data[$key]['acceptedOffer'] = $acceptedOffer;
					}
					// ACTION
					if (in_array('action',$properties)) {
						$dataHistory = ApiFactory::request()->type('action')->get(['targetCollection'=>$idorder,'properties'=>'agent','orderBy'=>'endTime','ordering'=>'desc'])->ready();
						if (isset($dataHistory[0])) {
							$data[$key]['potentialAction'] = ApiFactory::response()->type('action')->setData($dataHistory)->ready();
						}
					}
					// CUSTOMER
					if (in_array('customer',$properties)) {
						$customerType = $value['type'];
						$customerData = $value;
						unset($customerData['customer']);
						unset($customerData['seller']);
						unset($customerData['orderDate']);
						unset($customerData['paymentDueDate']);
						unset($customerData['orderStatus']);
						$data[$key]['customer'] = ApiFactory::response()->type($customerType)->setData($customerData)->ready();
						$data[$key]['type'] = "Order";
					}
					// INVOICE
					if (in_array('invoice',$properties )) {
						$dataInvoice = ApiFactory::request()->type('invoice')->get(['referencesOrder'=>$idorder,'orderBy'=>'scheduledPaymentDate','ordering'=>'desc'])->ready();
						if(isset($dataInvoice[0])) {
							$data[$key]['partOfInvoice'] = ApiFactory::response()->type('invoice')->setData($dataInvoice)->ready();
						}
					}
					// ORDER ITEM
					if (in_array('orderedItem',$properties) && !in_array('acceptedOffer',$properties)) {
						$orderItemParams = ['orderItemNumber'=>$idorder,'properties'=>'orderedItem'];
						if (isset($params['orderedItem'])) {
							$orderItemParams = array_merge($orderItemParams, ['orderedItem' => $params['orderedItem']]);
						}
						if (isset($params['idservice'])) {
							$orderItemParams = array_merge($orderItemParams, ['idservice' => $params['idservice']]);
						}
						$dataOrderItem = ApiFactory::request()->type('orderItem')->get($orderItemParams)->ready();
						if(isset($dataOrderItem[0]) && isset($data[$key])) {
							$data[$key]['orderedItem'] = ApiFactory::response()->type('orderItem')->setData($dataOrderItem)->ready();
						} else {
							unset($data[$key]);
						}
					}
					// SELLER
					if (in_array('seller',$properties) && isset($data[$key])) {
						$seller = $value['seller'];
						$sellerData = ApiFactory::request()->type('thing')->get(['idthing'=>$seller] + $params)->ready();
						if (isset($sellerData[0])) {
							$sellerType = lcfirst($sellerData[0]['type']);
							$dataSeller = ApiFactory::request()->type($sellerType)->get(['thing'=>$seller] + $params)->ready();
							if(isset($dataSeller[0])) {
								$data[$key]['seller'] = ApiFactory::response()->type($sellerType)->setData($dataSeller[0])->ready();
							}
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
	public function post(?array $params = null, array $uploadfiles = null): array
	{
		$params['dateCreated'] = date("Y-m-d H:i:s");
		return parent::post($params);
	}

}
