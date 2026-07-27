<?php
namespace Plinct\Api\Application\Entities\Thing;

use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class ThingUpdateUseCase
{
	public function __construct(
		protected DatabaseActions $databaseActions
	) { }

	/**
	 * @param string $type
	 * @param array $sanitizedParams
	 * @return array
	 */
	public function update(string $type, array $sanitizedParams): array
	{
		return ['status' => false, 'message' => 'Not implemented yet'];
	}

	/**
	 * @param string $idHasPart
	 * @param string $idIsPartOf
	 * @param string|null $caption
	 * @param int|null $position
	 * @param string $representativeOfPage
	 * @return array
	 * @throws ReflectionException
	 */
	protected function updateRelationship(string $idHasPart, string $idIsPartOf, string $caption = null, int $position = null, string $representativeOfPage = ''): array
	{
		$responsedatabase = $this->databaseActions->updateRelationship($idHasPart, $idIsPartOf, $caption, $position, $representativeOfPage);
		if ($responsedatabase['status'] === false) {
			return $responsedatabase;
		}
		// SE REPRESENTATIVE OF PAGE FOR TRUE, ALTERAR IMAGE EM THING
		if ($representativeOfPage) {
			// PEGAR CAMINHO DA IMAGEM DE IS PART OF
			$getDataisPartOf = $this->databaseActions->read('imageObject', ['thing'=>$idIsPartOf]);
			$getDataisPartOf->setJoin('creativeWork', 'creativeWork.thing = imageObject.thing');
			$getDataisPartOf->setJoin('mediaObject', 'mediaObject.thing = imageObject.thing');
			$getDataisPartOf->setFields('imageObject.thing, contentUrl, thing.type, thing.idthing');
			$isPartOfData = $getDataisPartOf->render();
			if ($isPartOfData['status'] === false) {
				return $isPartOfData;
			}
			if (empty($isPartOfData['data'])) {
				return ['status'=> false, 'message'=> 'Thing is part of not found', 'data'=>[]];
			}
			$isPartOf = $isPartOfData['data'][0];
			$image = $isPartOf['contentUrl'];
			// ALTERAR IMAGEM EM THING
			$hasPartData = $this->databaseActions->update('thing', ['image' => $image], ['idthing' => $idHasPart]);
			if ($hasPartData['status'] === false) {
				return $hasPartData;
			}
			return ['status'=> true, 'message'=> 'Representative of page updated', 'data'=>$isPartOfData['data']];
		}
		return $responsedatabase;
	}
}
