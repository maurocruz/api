<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\ImageObject;

use Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\MediaObjectReadUseCase;
use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Application\Support\SupportApplication;

class ImageObjectReadUseCase extends MediaObjectReadUseCase
{

	public function read(array $queryParams = []): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SanitizeParams::sanitize('image_object', $queryParams);

		// OBTÉM DADOS DO BANCO DE DADOS
		$idHasPart = $sanitizedParams['idHasPart'] ?? $sanitizedParams['isPartOf'] ?? null;
		if ($idHasPart) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart'=>$idHasPart, 'typeIsPartOf' => 'ImageObject'] + $sanitizedParams);
			$getData->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf');
			$getData->setJoin('creativeWork', 'creativeWork.thing = thing_has_thing.idIsPartOf');
			$getData->setJoin('mediaObject', 'mediaObject.thing = thing_has_thing.idIsPartOf');
			$getData->setJoin('imageObject', 'imageObject.thing = thing_has_thing.idIsPartOf');
		} else {
			$getData = $this->databaseActions->read('imageObject', $sanitizedParams);
			$getData->setJoin('creativeWork', 'creativeWork.thing = imageObject.thing');
			$getData->setJoin('mediaObject', 'mediaObject.thing = imageObject.thing');
		}
		$databaseData = $getData->render();

		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		return SupportApplication::returnDTOFromDatabaseData($databaseData,  'ImageObject found');
	}
}
