<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\OrderItem;

use Plinct\Api\Application\Entities\Thing\Intangible\Offer\OfferReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class OrderItemReadUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions,
		private OfferReadUseCase $offerReadUseCase
	)	{	}

	/**
	 * @throws ReflectionException
	 */
	public function read(array $queryParams = []): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SupportApplication::sanitizeParams('OrderItem', $queryParams);
		$properties = $sanitizedParams['properties'] ?? [];

		// BUSCA DADOS DO BANCO DE DADOS
		$databaseData = $this->databaseActions->read('orderItem', $sanitizedParams)->render();
		if ($databaseData['status'] === false) {
			return $databaseData;
		}

		foreach ($databaseData['data'] as $key => $value) {
			$databaseData['data'][$key]['type'] = 'OrderItem';
			// ORDERED ITEM
			if (in_array('orderedItem', $properties)) {
				$dataOrdereditem = $this->offerReadUseCase->read(['thing' => $value['orderedItem'], 'properties' => 'itemOffered']);
				if ($dataOrdereditem['status'] === false) {
					return $dataOrdereditem;
				}
				$databaseData['data'][$key]['orderedItem'] = $dataOrdereditem['data'];
			}
		}
		return $databaseData;
	}
}
