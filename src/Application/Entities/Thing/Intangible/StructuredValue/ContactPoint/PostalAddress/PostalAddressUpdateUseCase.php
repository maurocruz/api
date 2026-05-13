<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\ContactPoint\PostalAddress;

use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class PostalAddressUpdateUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function update(array $parseBody = []): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SanitizeParams::sanitize('postalAddress', $parseBody);

		// VALIDAR ENTRADAS
		$idpostalAddress = $sanitizedParams['idpostalAddress'] ?? null;
		if (!$idpostalAddress) {
			return ['status' => false, 'message' => 'Missing required fields'];
		}

		// ATUALIZAR POSTAL ADDRESS
		$databaseData = $this->databaseActions->update('postalAddress', $sanitizedParams, ['idpostalAddress' => $idpostalAddress]);
		if ($databaseData['status'] === false) {
			return $databaseData;
		}

		// OBTER O ITEM ATUALIZADO
		$dataItem = $this->databaseActions->read('postalAddress', ['idpostalAddress' => $idpostalAddress])->render();
		if ($dataItem['status'] === false) {
			return $dataItem;
		}
		foreach ($dataItem['data'] as $key => $value) {
			$dataItem['data'][$key]['type'] = 'PostalAddress';
		}
		return ['status' => true, 'message' => 'Update postalAddress sucessfully', 'data'=>$dataItem['data'] ];
	}
}
