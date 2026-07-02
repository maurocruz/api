<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\ImageObject;

use Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\MediaObjectReadUseCase;

class ImageObjectReadUseCase extends MediaObjectReadUseCase
{

	public function read(array $sanitizedParams): array
	{
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
		return $getData->render();
	}
}
