<?php
namespace Plinct\Api\Application\Entities\Thing\Taxon;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use ReflectionException;

class TaxonReadUseCase extends ThingReadUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
		$properties = $sanitizedParams['properties'] ?? [];

		$getData = $this->databaseActions->read('taxon', $sanitizedParams);
		$databaseData =  $getData->render();
		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		foreach ($databaseData['data'] as $key => $taxon) {
			if (in_array('childTaxon', $properties)) {
				$getDataChildTaxon = $this->databaseActions->read('taxon', ['parentTaxon' => $taxon['idtaxon']]);
				$databaseChildTaxon = $getDataChildTaxon->render();
				if ($databaseChildTaxon['status'] === false) {
					return $databaseChildTaxon;
				}
				$databaseData['data'][$key]['childTaxon'] = $databaseChildTaxon['data'];
			}
		}
		return $databaseData;
	}
}
