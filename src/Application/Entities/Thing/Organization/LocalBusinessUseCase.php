<?php
namespace Plinct\Api\Application\Entities\Thing\Organization;

use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Domain\Entities\Thing\Organization\LocalBusiness;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class LocalBusinessUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
		$properties = $sanitizedParams['properties'] ?? [];

		// OBTÉM DADOS DO BANCO DE DADOS
		$getData = $this->databaseActions->read('localBusiness', $sanitizedParams);
		$getData->setJoin('organization', 'organization.thing = localBusiness.thing');

		// AGGREGATE RATING
		if (in_array('aggregateRating', $properties)) {
			$getData->setFields('*,AVG(reviewRating) as ratingValue, COUNT(reviewRating) as reviewCount');
			$getData->setJoin('review','`review`.itemReviewed = `thing`.idthing', null, 'LEFT');
			$getData->setParams(['groupBy' => 'idlocalBusiness']);
		}

		// LOCATION
		if (in_array('location', $properties)) {
			$getData->setJoin('place', 'place.thing = organization.location');
			$getData->setJoin('geoCoordinates','`geoCoordinates`.idgeoCoordinates = `place`.geo', null, 'LEFT');
			$getData->setJoin('postalAddress','`postalAddress`.idpostalAddress = `geoCoordinates`.address', null, 'LEFT');
		}
		$data = $getData->render();
		if ($data['status'] === false) {
			return $data;
		}

		$dataObject = array_map(fn($value) => new LocalBusiness($value), $data['data']);

		return SupportApplication::returnDTO(true, 'LocalBusiness found', $dataObject);
	}
}
