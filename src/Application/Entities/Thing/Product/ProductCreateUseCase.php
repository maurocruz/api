<?php
namespace Plinct\Api\Application\Entities\Thing\Product;

use Plinct\Api\Application\Entities\Thing\ThingCreateUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

class ProductCreateUseCase extends ThingCreateUseCase
{

	/**
	 * @throws ReflectionException
	 */
	public function create(array $sanitizedParams): array
	{
		// VALIDAR ENTRADAS
		$name = $sanitizedParams['name'] ?? null;
		if (!$name) {
			return SupportApplication::returnDTO(false, 'Name is required');
		}

		// CRIAR THING
		$sanitizedParams['type'] = 'Product';
		$thingCreatedData = parent::create($sanitizedParams);

		if ($thingCreatedData['status'] === false) {
			return $thingCreatedData;
		}
		$thingId = $thingCreatedData['data'][0]['idthing'];

		// SALVAR PRODUTO
		$sanitizedParams['thing'] = $thingId;
		return $this->databaseActions->create('product', $sanitizedParams);
	}
}
