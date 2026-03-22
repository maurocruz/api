<?php
namespace Plinct\Api\Application\Entities\Thing;

use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;

class ThingCreateUseCase
{
	public function __construct(
		protected readonly DatabaseActions $databaseActions
	) { }

	public function create(array $parseBody): array
	{
		$name = $parseBody['name'] ?? null;
		$type = $parseBody['type'] ?? null;
		if (!$name || !$type) {
			return ['status'=> false, 'message'=> 'Name and type is required'];
		}
		// SANITIZE PARAMS
		$parseBody = SupportApplication::sanitizeParams('thing', $parseBody);		// GET ENTITY
		// BUSCAR ENTIDADE
		$entity = SupportApplication::getEntity('thing');
		// HIDRATAR
		$paramsHidrated = $entity->hydrateParams($parseBody);
		// SAVE THING
		return $this->databaseActions->create('Thing', $paramsHidrated);
	}
}
