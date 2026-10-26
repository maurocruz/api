<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\Order;

use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\OrderItem\OrderItemReadUseCase;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class OrderReadUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions,
		private OrderItemReadUseCase $orderItemReadUseCase
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
		$properties = $sanitizedParams['properties'] ?? [];
		$customerNameLike = $sanitizedParams['customerNameLike'] ?? null;
		$query = $sanitizedParams['query'] ?? null;
		if ($query === 'invoiceDue') {
			return $this->invoiceDue($sanitizedParams);
		}

		// OBTER DADOS DO BANCO DE DADOS
		$getData = $this->databaseActions->read('order', $sanitizedParams);
		if ($customerNameLike) {
			$getData->setJoin('thing','thing.idthing=`order`.customer');
			$getData->setWhere("`thing`.`name` LIKE '%$customerNameLike%'");
		}
		$dataBaseData = $getData->render();
		if ($dataBaseData['status'] === false) {
			return $dataBaseData;
		}

		foreach ($dataBaseData['data'] as $key => $value) {
			$dataBaseData['data'][$key]['type'] = 'Order';

			// ACCEPTED OFFER
			if (in_array('acceptedOffer', $properties)) {
				$dataAcceptedOffer = $this->databaseActions->read('orderItem', ['orderItemNumber' => $value['idorder']])
					->setJoin('offer', 'offer.thing = orderItem.orderedItem')
					->setJoin('thing', 'thing.idthing = orderItem.orderedItem')
					->render();
				if ($dataAcceptedOffer['status'] === false) {
					return $dataAcceptedOffer;
				}
				$dataBaseData['data'][$key]['acceptedOffer'] = $dataAcceptedOffer['data'];
			}

			// ORDERED ITEM
			if (in_array('orderedItem', $properties)) {
				$dataOrderItem = $this->orderItemReadUseCase->read(['orderItemNumber' => $value['idorder'], 'properties' => 'acceptedOffer']);
				if ($dataOrderItem['status'] === false) {
					return $dataOrderItem;
				}
				$dataBaseData['data'][$key]['orderedItem'] = $dataOrderItem['data'];
			}
			// CUSTOMER
			if (in_array('customer', $properties) && isset($value['customer'])) {
				$dataCustomer = $this->databaseActions->read('thing', ['idthing' => $value['customer']])->render();
				if ($dataCustomer['status'] === false) {
					return $dataCustomer;
				}
				$dataBaseData['data'][$key]['customer'] = $dataCustomer['data'][0];
			}
			// SELLER
			if (in_array('seller', $properties)) {
				$dataSeller = $this->databaseActions->read('thing', ['idthing' => $value['seller'], 'hasPart' => true])
					->setJoin('organization', 'organization.thing = thing.idthing')
					->render();
				if ($dataSeller['status'] === false) {
					return $dataSeller;
				}
				$dataBaseData['data'][$key]['seller'] = $dataSeller['data'][0];
			}
		}
		return $dataBaseData;
	}

	/**
	 * @throws ReflectionException
	 */
	private function invoiceDue(array $sanitizedParams): array
	{
		$getData = $this->databaseActions->read('order', $sanitizedParams);
		$getData->setFields("
		order.idorder, 
		order.orderDate, 
		order.paymentDueDate, 
		t.name, 
		count(idorder) as numberOfInvoices, 
		SUM(invoice.paymentStatus = 'PaymentDue') AS numberOfInvoicesPaymentDue, 
		MIN(CASE WHEN invoice.paymentStatus = 'PaymentDue' THEN invoice.scheduledPaymentDate END) AS nextScheduledPaymentDate");
		$getData->setJoin('invoice', 'invoice.referencesOrder = order.idorder');
		$getData->setJoin('thing','t.idthing= order.customer','t');
		$getData->setParams(['groupBy' => 'idorder', 'orderBy' => 'scheduledPaymentDate', 'ordering' => 'desc']);
		$data = $getData->render();

		if ($data['status'] === false) {
			return $data;
		}
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['type'] = 'Order';
		}
		return $data;
	}
}
