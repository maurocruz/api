<?php
namespace Plinct\Api\Application\Entities\Thing\Place;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Domain\Entities\Thing\Place\Place;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

class PlaceReadUseCase extends ThingReadUseCase
{
	public function __construct(
		DatabaseActions $databaseActions
	) {
		parent::__construct($databaseActions);
	}

	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams = []): array
	{
		$properties = $sanitizedParams['properties'] ?? [];

		// SE HOUVER IDHASPART
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		if ($idHasPart !== null) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart, 'typeIsPartOf'=>'Place']);
			$getData->setJoin('thing','thing.idthing=thing_has_thing.idIsPartOf');
			$getData->setJoin('place','place.thing=thing_has_thing.idIsPartOf');
			return $getData->render();
		}

		// OBTÉM DADOS DO BANCO DE DADOS
		$getData = $this->databaseActions->read('place', $sanitizedParams);
		// AGGREGATE RATING
		if (in_array('aggregateRating', $properties)) {
			$getData->setFields('*,AVG(reviewRating) as ratingValue, COUNT(reviewRating) as reviewCount');
			$getData->setJoin('review','`review`.itemReviewed = `thing`.idthing', null, 'LEFT');
			$getData->setParams(['groupBy' => 'idplace']);
		}
		// GEO
		if (in_array('geo', $properties)) {
			$getData->setJoin('geoCoordinates','`geoCoordinates`.idgeoCoordinates = `place`.geo', null, 'LEFT');
			$getData->setJoin('postalAddress','`postalAddress`.idpostalAddress = `geoCoordinates`.address', null, 'LEFT');
		}
		$data = $getData->render();

		if ($data['status'] === false) {
			return $data;
		}

		$dataObject = array_map(fn($value) => (new Place($value)), $data['data']);

		return SupportApplication::returnDTO(true, 'Place found', $dataObject);
	}
}
