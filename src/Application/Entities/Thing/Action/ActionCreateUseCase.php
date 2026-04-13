<?php
namespace Plinct\Api\Application\Entities\Thing\Action;

use Plinct\Api\Application\Entities\Thing\ThingCreateUseCase;
use Plinct\Api\Application\Support\SupportApplication;

class ActionCreateUseCase extends ThingCreateUseCase
{

	public function create(array $parseBody): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SupportApplication::sanitizeParams('action', $parseBody);
		$sanitizedParams['type'] = 'Action';

		// VERIFICA CAMPOS OBRIGATÓRIOS
		$object = $sanitizedParams['object'] ?? null;
		$agent = $sanitizedParams['agent'] ?? null;
		if (!$object || !$agent) {
			return ['status' => false, 'message' => 'Object and agent is required'];
		}
		$sanitizedParams['actionStatus'] = $sanitizedParams['actionStatus'] ?? "PotentialActionStatus";
		$sanitizedParams['startTime'] = $sanitizedParams['startTime'] ?? date('Y-m-d H:i:s');

		if (isset($sanitizedParams['provider']) && !is_numeric($sanitizedParams['provider'])) {
			unset($sanitizedParams['provider']);
		}

		// SALVA THING
		$dataThing = parent::create($sanitizedParams);
		if ($dataThing['status'] === false) {
			return $dataThing;
		}
		$valueThing = $dataThing['data'][0];
		$idthing = $valueThing['idthing'];

		// SALVA ACTION
		$dataAction = $this->databaseActions->create('action', ['thing' => $idthing] + $sanitizedParams);
		if ($dataAction['status'] === false) {
			return $dataAction;
		}
		return SupportApplication::returnDTOFromDatabaseData($dataAction);
	}
}
