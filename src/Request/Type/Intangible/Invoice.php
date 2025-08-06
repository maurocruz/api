<?php
namespace Plinct\Api\Request\Type\Intangible;

use DateTime;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Invoice extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('invoice');
	}

	/**
   * @param array $params
   * @return array
   */
  public function get(array $params = []): array
  {
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$data = parent::getData($params);
		if ($properties) {
			foreach ($data as $key => $value) {
				$referencesOrder = $value['referencesOrder'];
				$customer = $value['customer'];
				$providerData = $value['provider'];
				// CUSTOMER
				if (in_array('customer', $properties)) {
					$dataCustomer = ApiFactory::request()->type('thing')->get(['idthing'=>$customer,'hasPart'=>true])->ready();
					if (isset($dataCustomer[0])) {
						$customerType = $dataCustomer[0]['type'];
						$data[$key]['customer'] = ApiFactory::response()->type($customerType)->setData($dataCustomer[0])->ready();
					}
				}
				// PROVIDER
				if (in_array('provider', $properties)) {
					$dataProvider = ApiFactory::request()->type('thing')->get(['idthing'=>$providerData,'hasPart'=>true])->ready();
					if (isset($dataProvider[0])) {
						$providerType = $dataProvider[0]['type'];
						$data[$key]['provider'] = ApiFactory::response()->type($providerType)->setData($dataProvider[0])->ready();
					}
				}
				// REFERENCES ORDER
				if (in_array('referencesOrder', $properties)) {
					$paramsOrder = array_key_exists('orderStatus', $params) ? ['orderStatus' => $params['orderStatus']] : [];
					$paramsOrder = in_array('orderedItem',$properties) ? $paramsOrder + ['properties'=>'orderedItem'] : $paramsOrder;
					$dataOrder = ApiFactory::request()->type('order')->get(['idorder' => $referencesOrder] + $paramsOrder)->ready();
					if (isset($dataOrder[0])) {
						$data[$key]['referencesOrder'] = ApiFactory::response()->type('order')->setData($dataOrder[0])->ready();
					} else {
						unset($data[$key]);
					}
				}
			}
		}
    return parent::sortData(array_values($data));
  }

	/**
	 * @param array|null $params
	 * @param array|null $uploadfiles
	 * @return array
	 */
	public function post(array $params = null, array $uploadfiles = null): array
	{
		$dataPost = parent::post($params);
		if (isset($dataPost['status']) && $dataPost['status'] == 'success') {
			$dataReturn = $dataPost['data'][0];
			$time = (new DateTime())->format('Y-m-d H:i:s');
			ApiFactory::request()->type('action')->post([
				'actionStatus' => 'CompletedActionStatus',
				'agent' => ApiFactory::user()->userLogged()->getIduser(),
				'object' => $dataReturn['idinvoice'],
				'result' => str_replace("&","; ",http_build_query($dataReturn)),
				'startTime' => $time,
				'endTime' => $time,
				'targetCollection' => $dataReturn['referencesOrder'],
				'type'=>'AddAction'
			])->ready();
		}
		return $dataPost;
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		$dataPut = parent::put($params);
		if (isset($dataPut['status']) && $dataPut['status'] == 'success') {
			$dataReturn = $dataPut['data'][0];
			$time = (new DateTime())->format('Y-m-d H:i:s');
			ApiFactory::request()->type('action')->post([
				'actionStatus'=>'CompletedActionStatus',
				'agent' => ApiFactory::user()->userLogged()->getIduser(),
				'object' => $dataReturn['idinvoice'],
				'result' => str_replace("&","; ",http_build_query($dataReturn)),
				'startTime' => $time,
				'endTime' => $time,
				'targetCollection' => $dataReturn['referencesOrder'],
				'type' => 'ReplaceAction'
			])->ready();
		}
		return $dataPut;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		$idinvoice = $params['idinvoice'] ?? null;
		if ($idinvoice) {
			$dataDelete = parent::delete(['idinvoice'=>$idinvoice]);
			if (isset($dataDelete['status']) && $dataDelete['status'] == 'success') {
				$time = (new DateTime())->format('Y-m-d H:i:s');
				ApiFactory::request()->type('action')->post([
					'actionStatus'=>'CompletedActionStatus',
					'agent' => ApiFactory::user()->userLogged()->getIduser(),
					'object' => $params['idinvoice'],
					'result' => str_replace("&","; ",http_build_query($params)),
					'startTime' => $time,
					'endTime' => $time,
					'targetCollection' => $params['referencesOrder'],
					'type' => 'DeleteAction'
				])->ready();
			}
			return $dataDelete;
		}
		return false;
	}
}
