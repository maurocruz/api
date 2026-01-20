<?php
namespace Plinct\Api\Request\Type\Intangible;

use DateTime;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;

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
		$getData = new GetData('invoice');
		$getData->setParams($params);
		// REFERENCES ORDER
		if (in_array('referencesOrder', $properties)) {
			$getData->setLeftJoin('order','`order`.idorder=`invoice`.referencesOrder');
		}
		// CUSTOMER
		if (in_array('customer', $properties) && !in_array('provider', $properties)) {
			$getData->setLeftJoin('thing','`thing`.idthing=`invoice`.customer', 'cs');
		}
		// PROVIDER
		if (in_array('provider', $properties) && !in_array('customer', $properties)) {
			$getData->setLeftJoin('thing','`thing`.idthing=`invoice`.provider', 'pr');
		}
		$data = $getData->render();

		if ($properties) {
			foreach ($data as $key => $value) {
				// REFERENCES ORDER
				if (in_array('referencesOrder', $properties)) {
					$referencesOrderData = [
						"idorder" => $value['referencesOrder'],
						"orderDate" => $value['orderDate'],
						"paymentDueDate" => $value['paymentDueDate'],
						"tags" => $value['tags'],
						"orderStatus" => $value['orderStatus'],
						"discount" => $value['discount'],
						"seller" => $value['seller'],
					];
					$data[$key]['referencesOrder'] = ApiFactory::response()->type('order')->setData($referencesOrderData)->ready();
					unset($data[$key]['tags']);
					unset($data[$key]['orderDate']);
					unset($data[$key]['paymentDueDate']);
					unset($data[$key]['orderStatus']);
					unset($data[$key]['discount']);
					unset($data[$key]['seller']);
				}
				// CUSTOMER
				if (in_array('customer', $properties) && !in_array('provider', $properties)) {
					$customerData = [
						"idthing" => $value['customer'] ?? null,
						"additionalType" => $value['addtionalType'] ?? null,
						"alternateName" => $value['alternateName'] ?? null,
						"dateRegistered" => $value['dateRegistered'] ?? null,
						"lastModified" => $value['lastModified'] ?? null,
						"description" => $value['description'] ?? null,
						"disambiguatingDescription" => $value['disambiguatingDescription'] ?? null,
						"image" => $value['image'] ?? null,
						"mainEntityOfPage" => $value['mainEntityOfPage'] ?? null,
						"name" => $value['name'] ?? null,
						"sameAs" => $value['sameAs'] ?? null,
						"type" => $value['type'] ?? null,
						"url" => $value['url'] ?? null
					];
					$data[$key]['customer'] = ApiFactory::response()->type('thing')->setData($customerData)->ready();
					unset($data[$key]['addtionalType']);
					unset($data[$key]['alternateName']);
					unset($data[$key]['dateRegistered']);
					unset($data[$key]['lastModified']);
					unset($data[$key]['description']);
					unset($data[$key]['disambiguatingDescription']);
					unset($data[$key]['image']);
					unset($data[$key]['mainEntityOfPage']);
					unset($data[$key]['name']);
					unset($data[$key]['sameAs']);
					unset($data[$key]['type']);
					unset($data[$key]['url']);
				}
				// PROVIDER
				if (in_array('provider', $properties) && !in_array('customer', $properties)) {
					$providerData = [
						"idthing" => $value['provider'] ?? null,
						"additionalType" => $value['addtionalType'] ?? null,
						"alternateName" => $value['alternateName'] ?? null,
						"dateRegistered" => $value['dateRegistered'] ?? null,
						"lastModified" => $value['lastModified'] ?? null,
						"description" => $value['description'] ?? null,
						"disambiguatingDescription" => $value['disambiguatingDescription'] ?? null,
						"image" => $value['image'] ?? null,
						"mainEntityOfPage" => $value['mainEntityOfPage'] ?? null,
						"name" => $value['name'] ?? null,
						"sameAs" => $value['sameAs'] ?? null,
						"type" => $value['type'] ?? null,
						"url" => $value['url'] ?? null
					];
					$data[$key]['provider'] = ApiFactory::response()->type('thing')->setData($providerData)->ready();
					unset($data[$key]['addtionalType']);
					unset($data[$key]['alternateName']);
					unset($data[$key]['dateRegistered']);
					unset($data[$key]['lastModified']);
					unset($data[$key]['description']);
					unset($data[$key]['disambiguatingDescription']);
					unset($data[$key]['image']);
					unset($data[$key]['mainEntityOfPage']);
					unset($data[$key]['name']);
					unset($data[$key]['sameAs']);
					unset($data[$key]['type']);
					unset($data[$key]['url']);
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
		$paymentDue = $params['paymentDueDate'] ?? null;
		if ($paymentDue == '') {
			unset($params['paymentDueDate']);
		}
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
		$referencesOrder = $params['referencesOrder'] ?? null;
		if ($idinvoice && $referencesOrder) {
			$dataDelete = parent::delete(['idinvoice'=>$idinvoice]);
			if (isset($dataDelete['status']) && $dataDelete['status'] == 'success') {
				$time = (new DateTime())->format('Y-m-d H:i:s');
				ApiFactory::request()->type('action')->post([
					'actionStatus'=>'CompletedActionStatus',
					'agent' => ApiFactory::user()->userLogged()->getIduser(),
					'object' => $idinvoice,
					'result' => str_replace("&","; ",http_build_query($params)),
					'startTime' => $time,
					'endTime' => $time,
					'targetCollection' => $referencesOrder,
					'type' => 'DeleteAction'
				])->ready();
			}
			return $dataDelete;
		}
		return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: idinvoice and referencesOrder']);
	}
}
