<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\Invoice;

use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class InvoiceUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	)	{	}

	/**
	 * @throws ReflectionException
	 */
	public function create(array $sanitizedParams): array
	{
		$totalPaymentDue = $sanitizedParams['totalPaymentDue'] ?? null;
		$scheduledPaymentDate = $sanitizedParams['scheduledPaymentDate'] ?? null;
		$referencesOrder = $sanitizedParams['referencesOrder'] ?? null;
		$customer = $sanitizedParams['customer'] ?? null;
		$provider = $sanitizedParams['provider'] ?? null;

		if (!$totalPaymentDue || !$scheduledPaymentDate || !$referencesOrder || !$customer || !$provider) {
			return ['status' => false, 'message' => 'Missing required fields', 'data' =>[]];
		}

		$databaseData = $this->databaseActions->create('invoice', $sanitizedParams);
		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		foreach ($databaseData['data'] as $key => $value) {
			$databaseData['data'][$key]['type'] = 'Invoice';
		}
		return $databaseData;
	}

	public function update(array $sanitizedParams): array
	{
		$idinvoice = $sanitizedParams['idinvoice'] ?? null;
		if (!$idinvoice) {
			return ['status' => false, 'message' => 'Missing required fields'];
		}
		$databaseData = $this->databaseActions->update('invoice', $sanitizedParams, ['idinvoice' => $idinvoice]);
		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		foreach ($databaseData['data'] as $key => $value) {
			$databaseData['data'][$key]['type'] = 'Invoice';
		}
		return $databaseData;
	}

	/**
	 * @throws ReflectionException
	 */
	public function delete(array $sanitizedParams): array
	{
		$idinvoice = $sanitizedParams['idinvoice'] ?? null;
		if (!$idinvoice) {
			return ['status' => false, 'message' => 'Missing required fields'];
		}
		// OBTEM O ITEM
		$databaseGetItem = $this->databaseActions->read('invoice', ['idinvoice' => $idinvoice])->render();
		if ($databaseGetItem['status'] === false) {
			return $databaseGetItem;
		}
		$databaseData = $this->databaseActions->delete('invoice', ['idinvoice' => $idinvoice]);
		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		foreach ($databaseGetItem['data'] as $key => $value) {
			$databaseGetItem['data'][$key]['type'] = 'Invoice';
		}
		return ['status' => true, 'message' => 'Invoice item deleted!', 'data' => $databaseGetItem['data']];
	}
}
