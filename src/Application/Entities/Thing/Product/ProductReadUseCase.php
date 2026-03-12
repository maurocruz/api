<?php
namespace Plinct\Api\Application\Entities\Thing\Product;

use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class ProductReadUseCase
{
	public function __construct(private DatabaseActions $databaseActions)
	{}

	/**
	 * @throws ReflectionException
	 */
	public function read(array $params = []): array
	{
		// SANITIZE PARAMS
		$params = SanitizeParams::sanitize('product', $params);

		// BUSCA DADOS NO BANCO DE DADOS
		$getData = $this->databaseActions->read('product', $params);
		$databaseData = $getData->render();
		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		$itemsData = $databaseData['data'];

		// HIDRATA DADOS
		$entitiesHydrated = SupportApplication::hydrateFromDatabase($itemsData);

		// CONVERT DTO
		$convertDTO = SupportApplication::ConvertFromObjectToSchemaOrgArrayDTO($entitiesHydrated);

		// ENVIAR RESPONSE
		return ['status' => true, 'message' => 'Product read successfully', 'data' => $convertDTO];
	}
}
