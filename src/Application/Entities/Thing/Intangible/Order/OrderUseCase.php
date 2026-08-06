<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\Order;

use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class OrderUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	) {	}

	/**
	 * @throws ReflectionException
	 */
	public function create(array $sanitizedParams): array
	{
		$seller = $sanitizedParams['seller'] ?? null;
		if (!$seller) {
			return ['status' => false, 'message' => 'Missing required fields'];
		}
		return $this->databaseActions->create('order', ['seller' => $seller]);
	}

	/**
	 * @param array $sanitizedParams
	 * @return array
	 * @throws ReflectionException
	 */
	public function update(array $sanitizedParams): array
	{
		$idorder = $sanitizedParams['idorder'] ?? null;
		if (!$idorder) {
			return ['status' => false, 'message' => 'Missing required fields'];
		}
		$databaseData = $this->databaseActions->update('order', $sanitizedParams, ['idorder' => $idorder]);
		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		$databaseRead = $this->databaseActions->read('order', ['idorder' => $idorder])->render();
		if ($databaseRead['status'] === false) {
			return $databaseRead;
		}
		return SupportApplication::returnDTO(true,'Record in order updated', $databaseRead['data']);
	}

	/**
	 * @param array $sanitizedeParams
	 * @return array
	 * @throws ReflectionException
	 */
	public function delete(array $sanitizedeParams): array
	{
		$idorder = $sanitizedeParams['idorder'] ?? null;
		// OBTÉM O ITEM
		$databaseGetItem = $this->databaseActions->read('order', ['idorder' => $idorder])->render();
		if ($databaseGetItem['status'] === false) {
			return $databaseGetItem;
		}
		if (!$idorder) {
			return ['status' => false, 'message' => 'Missing required fields'];
		}
		$databaseDelete = $this->databaseActions->delete('order', ['idorder' => $idorder]);
		if ($databaseDelete['status'] === false) {
			return $databaseDelete;
		}
		return SupportApplication::returnDTO(true,'Deleted order successfully', $databaseDelete['data']);
	}
}
