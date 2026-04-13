<?php
namespace Plinct\Api\Application\Entities\Thing\Product;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

class ProductReadUseCase extends ThingReadUseCase
{

	/**
	 * @throws ReflectionException
	 */
	public function read(array $params): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SupportApplication::sanitizeParams('product', $params);

		// OBTÉM DADOS DO BANCO DE DADOS
		$databaseData = $this->databaseActions->read('product', $sanitizedParams)->render();
		if ($databaseData['status'] === false) {
			return $databaseData;
		}

		return SupportApplication::returnDTOFromDatabaseData($databaseData,  'Product found');
	}
}
