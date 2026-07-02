<?php
namespace Plinct\Api\Application\Entities\Thing\Product;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use ReflectionException;

class ProductReadUseCase extends ThingReadUseCase
{

	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
		// OBTÉM DADOS DO BANCO DE DADOS
		return $this->databaseActions->read('product', $sanitizedParams)->render();
	}
}
