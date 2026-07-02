<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\ContactPoint\PostalAddress;

use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class PostalAddressCreateUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	)	{ }

	/**
	 * @throws ReflectionException
	 */
	public function create(array $sanitizedParams): array
	{
		// VALIDAR ENTRADAS
		$streetAddress = $sanitizedParams['streetAddress'] ?? null;
		$addressLocality = $sanitizedParams['addressLocality'] ?? null;
		$addressRegion = $sanitizedParams['addressRegion'] ?? null;
		$addressCountry = $sanitizedParams['addressCountry'] ?? null;
		$postalCode = $sanitizedParams['postalCode'] ?? null;

		if (!$streetAddress && !$addressLocality && !$addressRegion && !$addressCountry && !$postalCode) {
			return ['status' => false, 'message' => 'Missing required fields', 'data' => []];
		}
		// SALVAR POSTAL ADDRESS
		$databaseData = $this->databaseActions->create('postalAddress', $sanitizedParams);
		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		foreach ($databaseData['data'] as $key => $value) {
			$databaseData['data'][$key]['type'] = 'PostalAddress';
		}
		return $databaseData;
	}
}
