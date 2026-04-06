<?php
namespace Plinct\Api\Application\Entities\Thing;

use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class ThingUpdateUseCase
{
	public function __construct(
		protected DatabaseActions $databaseActions
	) { }

	public function update(string $type, array $params): array
	{
		return ['status' => false, 'message' => 'Not implemented yet'];
	}

	/**
	 * @throws ReflectionException
	 */
	protected function updateRelationship(string $idHasPart, string $idIsPartOf, string $position = null, string $caption = null, string $representativeOfPage = null): array
	{
		$responsedatabase = $this->databaseActions->updateRelationship($idHasPart, $idIsPartOf, $position, $caption, $representativeOfPage);
		if ($responsedatabase['status'] === false) {
			return $responsedatabase;
		}
		// SE REPRESENTATIVE OF PAGE FOR TRUE, ALTERAR IMAGE EM THING
		if ($representativeOfPage) {
			// PEGAR CAMINHO DA IMAGEM DE IS PART OF
			$isPartOfData = $this->databaseActions->read('thing', ['idthing'=>$idIsPartOf])->setWithThings(false)->render();
			if ($isPartOfData['status'] === false) {
				return $isPartOfData;
			}
			$isPartOf = $isPartOfData['data'][0];
			$image = $isPartOf['image'];
			// ALTERAR IMAGEM EM THING
			$hasPartData = $this->databaseActions->update('thing', ['image' => $image], ['idthing' => $idHasPart]);
			if ($hasPartData['status'] === false) {
				return $hasPartData;
			}
		}
		return ['status'=> true, 'message'=> 'Representative of page updated', 'data'=>$responsedatabase['data']];
	}
}
