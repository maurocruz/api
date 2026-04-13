<?php
namespace Plinct\Api\Application\Entities\Thing\Action;

use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class ActionReadUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	)	{	}

	/**
	 * @throws ReflectionException
	 */
	public function read(array $params = []): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SanitizeParams::sanitize('action', $params);

		// OBTÉM DADOS DO BANCO DE DADOS
		$databaseData = $this->databaseActions->read('action', $sanitizedParams)->render();
		if ($databaseData['status'] === false) {
			return $databaseData;
		}

		// VERIFICA SE EXISTEM PROPRIEDADES REQUISITADAS
		$properties = $sanitizedParams['properties'] ?? null;
		if ($properties) {
			foreach ($databaseData['data'] as $key => $action) {
				// OBJECT
				if (in_array('object', $properties)) {
					$idobject = $action['object'];
					$databaseObject = $this->databaseActions->read('thing', ['idthing' => $idobject])->render();
					if ($databaseObject['status'] === false) {
						return $databaseObject;
					}
					$valueThing = $databaseObject['data'][0];
					$typeObject = $valueThing['type'];
					$dataTypeObject = $this->databaseActions->read(lcfirst($typeObject), ['thing' => $idobject])->render();
					if ($dataTypeObject['status'] === false) {
						return $dataTypeObject;
					}
					$valueType = $dataTypeObject['data'];
					$databaseData['data'][$key]['object'] = $valueType;
				}
			}
		}
		return SupportApplication::returnDTOFromDatabaseData($databaseData, 'Action found');
	}
}

