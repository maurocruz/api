<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork;

use Plinct\Api\Application\Entities\Thing\ThingCreateUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;

class CreativeWorkCreateUseCase extends ThingCreateUseCase
{
	public function __construct(DatabaseActions $databaseActions)
	{
		parent::__construct($databaseActions);
	}

	public function create(array $parseBody): array
	{
		// SANITIZE PARAMS
		$params = SupportApplication::sanitizeParams('creativeWork', $parseBody);

		// SALVAR THING
		$thingData =  parent::create($params);
		if ($thingData['status'] === false) {
			return $thingData;
		}
		$valueThing = $thingData['data'][0];
		$params['thing'] = $valueThing['idthing'];

		// SALVA CREATIVE WORK
		$entityCreativeWork = SupportApplication::getEntity('CreativeWork');
		$paramsHidrated = $entityCreativeWork->hydrateParams($params);
		return $this->databaseActions->create('creativeWork', $paramsHidrated);
	}
}
