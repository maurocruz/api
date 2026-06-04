<?php
namespace Plinct\Api\Application\Entities;

use Plinct\Api\Application\Entities\Thing\ThingCreateUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

class EntityCreateUseCase extends ThingCreateUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function createEntity(string $type, array $parseBody): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SupportApplication::sanitizeParams($type, $parseBody);

		//
		// SAVE THING
		$saveThing = parent::create($sanitizedParams);
		if($saveThing['status'] === false) {
			return $saveThing;
		} else {
			$thingData = $saveThing['data'][0];
			$thing = $thingData['idthing'];

			$hidratedParams = SupportApplication::hydrateOwnProperties($type, $sanitizedParams);
			$saveCreativework = $this->databaseActions->create($type, $hidratedParams + ['thing' => $thing]);

			if ($saveCreativework['status'] === false) {
				return $saveCreativework;
			} else {
				$creativeWorkData = $saveCreativework['data'];
				return ['status' => true, 'message' => "$type item has created", 'data' => $creativeWorkData];
			}
		}
	}
}
