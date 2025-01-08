<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;

class Invoice extends Entity
{
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
		$provider = $params['provider'] ?? null;
		if(array_key_exists('overdueInvoice', $params) && $provider) {
			$sql = "SELECT idinvoice, idorder, MIN(`invoice`.scheduledPaymentDate) as scheduledPaymentDate, SUM(`invoice`.totalPaymentDue) as totalPaymentDue, `thing`.name, COUNT(`invoice`.idinvoice) as overdueParcel, `order`.orderStatus FROM `order`
LEFT JOIN `invoice` ON `invoice`.referencesOrder=`order`.idorder
LEFT JOIN `thing` ON `thing`.idthing=`order`.customer
where `invoice`.provider = '$provider' AND `invoice`.paymentDueDate='0000-00-00' AND `invoice`.paymentStatus='PaymentDue' AND (`order`.orderStatus='OrderProcessing' OR `order`.orderStatus='OrderSupended')
group by `invoice`.referencesOrder
order by `invoice`.scheduledPaymentDate;";
			$data = PDOConnect::run($sql);
		} else {
			$properties = self::propertiesToArray($params['properties'] ?? null);
			$data = parent::getData($params);
			if ($properties) {
				foreach ($data as $key => $value) {
					$referencesOrder = $value['referencesOrder'];
					$customer = $value['customer'];
					// REFERENCES ORDER
					if (in_array('referencesOrder', $properties)) {
						if (array_key_exists('paymentDueDate', $params)) {
							unset($params['paymentDueDate']);
						}
						$dataOrder = ApiFactory::request()->type('order')->get(['idorder' => $referencesOrder] + $params)->ready();
						if (isset($dataOrder[0])) {
							$data[$key]['referencesOrder'] = ApiFactory::response()->type('order')->setData($dataOrder[0])->ready();
						} else {
							unset($data[$key]);
						}
					}
					// CUSTOMER
					if (in_array('customer', $properties)) {
						$dataCustomer = ApiFactory::request()->type('thing')->get(['idthing'=>$customer] + $params)->ready();
						if (isset($dataCustomer[0])) {
							$customerType = $dataCustomer[0]['type'];
							$data[$key]['customer'] = ApiFactory::response()->type($customerType)->setData($dataCustomer[0])->ready();
						}
					}
				}
			}
		}
    return parent::sortData(array_values($data));
  }
}
