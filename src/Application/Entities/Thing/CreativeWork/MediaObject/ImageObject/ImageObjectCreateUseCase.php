<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\ImageObject;

use Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\MediaObjectCreateUseCase;
use ReflectionException;

class ImageObjectCreateUseCase extends MediaObjectCreateUseCase
{
	/**
	 * @param array $sanitizedParams
	 * @param array $filesUpload
	 * @return array
	 * @throws ReflectionException
	 */
	public function create(array $sanitizedParams, array $filesUpload = []): array
	{
		// VERIFICA SE NÃO É APENAS RELACIONAMENTO
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		$idIsPartOf = $sanitizedParams['idIsPartOf'] ?? null;
		if ($idHasPart && $idIsPartOf) {
			return $this->createRelationship($idHasPart, $idIsPartOf);
		}

		// TODO - IMPLEMENTAR O RESTO DO CREATE
		return ['status'=> false, 'message'=> 'ImageObject create is not yet implemented'];
	}
}
