<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\ImageObject;

use Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\MediaObjectCreateUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

class ImageObjectCreateUseCase extends MediaObjectCreateUseCase
{
	/**
	 * @param array $parseBody
	 * @param array $filesUpload
	 * @return array
	 * @throws ReflectionException
	 */
	public function create(array $parseBody = [], array $filesUpload = []): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SupportApplication::sanitizeParams('ImageObject', $parseBody);

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
