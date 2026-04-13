<?php
namespace Plinct\Api\Application\Entities;

use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class EntityDeleteUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	)	{	}

	/**
	 * @throws ReflectionException
	 */
	public function delete(string $type, array $queryParams): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SanitizeParams::sanitize($type, $queryParams);

		// VERIFICA SE NÃO É APENAS RELACIONAMENTO
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		$idIsPartOf = $sanitizedParams['idIsPartOf'] ?? null;
		if ($idHasPart && $idIsPartOf) {
			return $this->databaseActions->removeRelationship($idHasPart, $idIsPartOf);
		}

		// VERIFICA SE O IDTHING EXISTE
		$idthing = $sanitizedParams['idthing'] ?? null;
		if (!$idthing) {
			return ['status' => false, 'message'=>'Idthing is required', 'data'=> []];
		}

		// BUSCA ITEM
		$itemData = $this->databaseActions->read($type, ['thing' => $idthing])->render();
		if ($itemData['status'] === false) {
			return $itemData;
		}

		// TODO SE MEDIAOBJECT, APAGAR ARQUIVOS ASSOCIADOS

		// APAGAR ITEM
		$responseDeleted = $this->databaseActions->delete('thing', ['idthing' => $idthing]);
		if ($responseDeleted['status'] === false) {
			return $responseDeleted;
		}

		// RETORNAR
		return SupportApplication::returnDTOFromDatabaseData($itemData, "Item '$type' deleted" );
	}
}
