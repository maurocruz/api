<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;

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
		$data = parent::getData($params);
		if (isset($data['error'])) {
			// ERROR
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		} elseif (!empty($data)) {
			foreach ($data as $key => $value) {
				// tags
				$data[$key]['identifier'][] = [
					'@type' => 'PropertyValue',
          'name' => 'tags',
          'value' => $value['tags']
				];
				unset($data[$key]['tags']);
				// properties
				if (!!$properties) {
					$idorder = $value['idorder'];
					// ACCEPTED OFFER
					if (in_array('acceptedOffer', $properties)) {
						$dataOrderItem = ApiFactory::request()->type('orderItem')->get(['orderItemNumber'=>$idorder,'properties'=>'offer,orderedItem']+$params)->ready();
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
						$dataHistory = ApiFactory::request()->type('action')->get(['object'=>$idorder,'properties'=>'agent','orderBy'=>'endTime','ordering'=>'desc'])->ready();
						if (isset($dataHistory[0])) {
							$data[$key]['potentialAction'] = ApiFactory::response()->type('action')->setData($dataHistory)->ready();
						}
					}
					// CUSTOMER
					if (in_array('customer',$properties)) {
						$customer = $value['customer'];
						$customerTypeData = PDOConnect::run("SELECT `type` FROM `thing` WHERE `idthing` = ? LIMIT 1", [$customer]);
						if (isset($customerTypeData[0])) {
							$customerType = lcfirst($customerTypeData[0]['type']);
							$dataCustomer = ApiFactory::request()->type($customerType)->get(['thing'=>$customer])->ready();
							if(isset($dataCustomer[0])) {
								$data[$key]['customer'] = ApiFactory::response()->type($customerType)->setData($dataCustomer[0])->ready();
							}
						}
					}
					// INVOICE
					if (in_array('invoice',$properties )) {
						$dataInvoice = ApiFactory::request()->type('invoice')->get(['referencesOrder'=>$idorder])->ready();
						if(isset($dataInvoice[0])) {
							$data[$key]['partOfInvoice'] = ApiFactory::response()->type('invoice')->setData($dataInvoice)->ready();
						}
					}
					// ORDER ITEM
					if (in_array('orderedItem',$properties) && !in_array('acceptedOffer',$properties)) {
						$dataOrderItem = ApiFactory::request()->type('orderItem')->get(['orderItemNumber'=>$idorder,'properties'=>'orderedItem'])->ready();
						if(isset($dataOrderItem[0])) {
							$data[$key]['orderedItem'] = ApiFactory::response()->type('orderItem')->setData($dataOrderItem)->ready();
						}
					}
					// SELLER
					if (in_array('seller',$properties)) {
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
	 * @return array
	 */
	public function post(?array $params = null): array
	{
		$params['dateCreated'] = date("Y-m-d H:i:s");
		return parent::post($params);
	}

}
