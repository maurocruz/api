<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\Offer;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use ReflectionException;

class OfferReadUseCase extends ThingReadUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
		$properties = $sanitizedParams['properties'] ?? [];
		$orderItemNumber = $sanitizedParams['orderItemNumber'] ?? null;

		// BUSCA DADOS NO BANCO DE DADOS
		if ($orderItemNumber) {
			$databaseData = $this->databaseActions->read('orderItem', ['orderItemNumber' => $orderItemNumber])
				->setJoin('offer', 'offer.thing = orderItem.orderedItem')
				->setJoin('thing', 'thing.idthing = orderItem.orderedItem')
				->render();
		} else {
			$databaseData = $this->databaseActions->read('offer', $sanitizedParams)->render();
		}
		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		foreach ($databaseData['data'] as $key => $value) {
			// ITEM OFFERED
			if (in_array('itemOffered', $properties)) {
				$dataItemOffered = $this->databaseActions->read('thing', ['idthing' => $value['itemOffered']])->render();
				if ($dataItemOffered['status'] === false) {
					return $dataItemOffered;
				}
				$databaseData['data'][$key]['itemOffered'] = $dataItemOffered['data'];
			}

			// OFFERED BY
			if (in_array('offeredBy', $properties)) {
				$dataOfferedBy = $this->databaseActions->read('thing', ['idthing' => $value['offeredBy']])->render();
				if ($dataOfferedBy['status'] === false) {
					return $dataOfferedBy;
				}
				$databaseData['data'][$key]['offeredBy'] = $dataOfferedBy['data'];
			}
		}
		return $databaseData;
	}
}
