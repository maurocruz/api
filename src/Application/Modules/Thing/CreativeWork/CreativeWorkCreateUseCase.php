<?php
namespace Plinct\Api\Application\Modules\Thing\CreativeWork;

use Plinct\Api\Application\Modules\Thing\ThingCreateUseCase;

class CreativeWorkCreateUseCase extends ThingCreateUseCase
{
	public function create(array $params = []): array
	{
		if (!isset($params['name'])) {
			return ['status' => false, 'message' => 'Name is required'];
		}
		// SAVE THING
		$saveThing = parent::create($params);
		if($saveThing['status'] === false) {
			return $saveThing;
		} else {
			$thingData = $saveThing['data'];
			$thing = $thingData['idthing'];

			$saveCreativework = $this->databaseActions->create('creativeWork', $params + ['thing' => $thing]);

			if($saveCreativework['status'] === false) {
				return $saveCreativework;
			}	else {
				$creativeWorkData = $saveCreativework['data'];
				return ['status' => true, 'message' => 'CreativeWork item has created', 'data' => $creativeWorkData];
			}
		}
	}
}
