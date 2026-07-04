<?php
namespace Plinct\Api\Application\Entities\Thing;

use Plinct\Api\Infrastructure\FileProcessor\FileProcessor;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

class ThingCreateUseCase
{
	public function __construct(
		protected readonly DatabaseActions $databaseActions,
		protected readonly FileProcessor $fileProcessor
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function create(array $sanitizedParams): array
	{
		// VALIDAR ENTRADAS
		$name = $sanitizedParams['name'] ?? null;
		$type = $sanitizedParams['type'] ?? null;
		if (!$name || !$type) {
			return ['status'=> false, 'message'=> 'Name and type is required'];
		}
		// PARAMETROS OBRIGATORIOS
		$sanitizedParams['dateCreated'] = $sanitizedParams['dateCreated'] ?? date('Y-m-d H:i:s');
		$sanitizedParams['dateModified'] = $sanitizedParams['dateModified'] ?? date('Y-m-d H:i:s');

		// SALVAR ITEM
		$responseData = $this->databaseActions->create('thing', $sanitizedParams);

		// RETORNAR
		if ($responseData['status'] === false) {
			return $responseData;
		}
		$thingData = $responseData['data'];
		$idthing = $thingData[0]['idthing'];

		// SALVAR RELATIONSHIP, SE HOUVER
		if (isset($sanitizedParams['idHasPart'])) {
			$relationship = $this->createRelationship($sanitizedParams['idHasPart'], $idthing);
			if ($relationship['status'] === false) {
				return $relationship;
			}
		}
		return ['status'=> true, 'message'=> 'Thing item has created', 'data'=>$thingData];

	}

	/**
	 * @throws ReflectionException
	 */
	protected function createRelationship(string $idHasPart, string $idIsPartOf): array
	{
		// OBTER TYPE HAS PART
		$dataTypeHasPart = $this->databaseActions->read('thing', ['idthing'=>$idHasPart, 'fields'=>'type'])->setWithThings(false)->render();
		if ($dataTypeHasPart['status'] === false) {
			return $dataTypeHasPart;
		}
		$valueTypeHasPart = $dataTypeHasPart['data'][0];
		$typeHasPart = $valueTypeHasPart['type'];

		// OBTER TYPE IS PART OF
		$dataTypeIsPartOf = $this->databaseActions->read('thing', ['idthing'=>$idIsPartOf])->setWithThings(false)->render();
		if ($dataTypeIsPartOf['status'] === false) {
			return $dataTypeIsPartOf;
		}
		$valueTypeIsPartOf = $dataTypeIsPartOf['data'][0];
		$typeIsPartOf = $valueTypeIsPartOf['type'];

		// SALVAR RELATIONAMENTO
		return $this->databaseActions->createRelationship($idHasPart, $typeHasPart, $idIsPartOf, $typeIsPartOf);
	}
}
