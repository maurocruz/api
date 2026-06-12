<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\OrderItem;

use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class OrderItemUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	) {	}

	/**
	 * @throws ReflectionException
	 */
	public function create(array $sanitizedParams): array
	{
		$orderedItem = $sanitizedParams['orderedItem'] ?? null;
		$orderItemNumber  = $sanitizedParams['orderItemNumber'] ?? null;
		if (!$orderedItem || !$orderItemNumber) {
			return ['status' => false, 'message' => 'Missing required fields'];
		}
		return $this->databaseActions->create('orderItem', ['orderedItem' => $orderedItem, 'orderItemNumber' => $orderItemNumber]);
	}

	/**
	 * @param array $sanitizedParams
	 * @return array
	 */
	public function update(array $sanitizedParams): array
	{
		$idorderItem = $sanitizedParams['idorderItem'] ?? null;
		if (!$idorderItem) {
			return ['status' => false, 'message' => 'Missing required fields'];
		}
		return $this->databaseActions->update('orderItem', $sanitizedParams, ['idorderItem' => $idorderItem]);
	}

	/**
	 * @param array $sanitizedeParams
	 * @return array
	 */
	public function delete(array $sanitizedeParams): array
	{
		$idorderItem = $sanitizedeParams['idorderItem'] ?? null;
		if (!$idorderItem) {
			return ['status' => false, 'message' => 'Missing required fields'];
		}
		return $this->databaseActions->delete('orderItem', ['idorderItem' => $idorderItem]);
	}
}
