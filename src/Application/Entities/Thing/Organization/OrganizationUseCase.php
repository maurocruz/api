<?php
namespace Plinct\Api\Application\Entities\Thing\Organization;

use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class OrganizationUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	) {	}

	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
		$properties = $sanitizedParams['properties'] ?? [];
		// OBTÉM DADOS DO BANCO DE DADOS
		$getData = $this->databaseActions->read('organization', $sanitizedParams);
		// LOCATION
		if (in_array('location', $properties)) {
			$getData->setJoin('place', 'place.thing = organization.location');
			$getData->setJoin('geoCoordinates','`geoCoordinates`.idgeoCoordinates = `place`.geo', null, 'LEFT');
			$getData->setJoin('postalAddress','`postalAddress`.idpostalAddress = `geoCoordinates`.address', null, 'LEFT');
		}
		return $getData->render();
	}
}
