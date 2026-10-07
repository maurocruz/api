<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\Invoice;

use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class InvoiceReadUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
		$properties = $sanitizedParams['properties'] ?? [];

		$getData = $this->databaseActions->read('invoice',$sanitizedParams);
		$data = $getData->render();
		if ($data['status'] === false) {
			return $data;
		}

		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['type'] = 'Invoice';
			// CUSTOMER
			$customer = $value['customer'] ?? null;
			if ($customer && in_array('customer',$properties)) {
				$customerData = $this->databaseActions->read('organization',['thing' => $customer])->render();
				if ($customerData['status'] === false) {
					return $customerData;
				}
				$data['data'][$key]['customer'] = $customerData['data'][0] ?? null;
			}
			// PROVIDER
			$provider = $value['provider'] ?? null;
			if ($provider && in_array('provider',$properties)) {
				$providerData = $this->databaseActions->read('organization',['thing' => $provider])->render();
				if ($providerData['status'] === false) {
					return $providerData;
				}
				$data['data'][$key]['provider'] = $providerData['data'][0];
			}
			// REFERENCES ORDER
			$referencesOrder = $value['referencesOrder'] ?? null;
			if ($referencesOrder && in_array('referencesOrder',$properties)) {
				$referencesOrderData = $this->databaseActions->read('order',['idorder' => $referencesOrder])->render();
				if ($referencesOrderData['status'] === false) {
					return $referencesOrderData;
				}
				$referencesOrderData['data'][0]['@type'] = 'Order';
				$data['data'][$key]['referencesOrder'] = $referencesOrderData['data'][0];
			}
		}
		return $data;
	}
}
