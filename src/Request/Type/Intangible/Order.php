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
		$properties = $params['properties'] ?? null;
		$data = parent::getData($params);
		if (isset($data['error'])) {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		} elseif (!empty($data)) {
			foreach ($data as $key => $value) {
				$data[$key]['identifier'][] = [
					'@type' => 'PropertyValue',
          'name' => 'tags',
          'value' => $value['tags']
				];
				unset($data[$key]['tags']);
				if ($properties) {
					// CUSTOMER
					if (stripos($properties, 'customer') !== false) {
						$customer = $value['customer'];
						$customerTypeData = PDOConnect::run("SELECT `type` FROM `thing` WHERE `idthing` = ? LIMIT 1", [$customer]);
						if (isset($customerTypeData[0])) {
							$customerType = lcfirst($customerTypeData[0]['type']);
							$dataCustomer = ApiFactory::request()->type($customerType)->get(['thing' => $customer])->ready();
							if(isset($dataCustomer[0])) {
								$data[$key]['customer'] = ApiFactory::response()->type($customerType)->setData($dataCustomer[0])->ready();
							}
						}
					}
					// SELLER
					if (stripos($properties, 'seller') !== false) {
						$seller = $value['seller'];
						$sellerTypeData = PDOConnect::run("SELECT `type` FROM `thing` WHERE `idthing` = ? LIMIT 1", [$seller]);
						if (isset($sellerTypeData[0])) {
							$sellerType = lcfirst($sellerTypeData[0]['type']);
							$dataSeller = ApiFactory::request()->type($sellerType)->get(['thing' => $seller])->ready();
							if(isset($dataSeller[0])) {
								$data[$key]['seller'] = ApiFactory::response()->type($sellerType)->setData($dataSeller[0])->ready();
							}
						}
					}
					// INVOICE
					if (stripos($properties, 'invoice') !== false) {
						$idorder = $value['idorder'];
						$dataInvoice = ApiFactory::request()->type('invoice')->get(['referencesOrder' => $idorder])->ready();
						if(isset($dataInvoice[0])) {
							$data[$key]['partOfInvoice'] = ApiFactory::response()->type('invoice')->setData($dataInvoice)->ready();
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
		$params['dateCreated'] = date("Y-m-d");
		return parent::post($params);
	}

}
