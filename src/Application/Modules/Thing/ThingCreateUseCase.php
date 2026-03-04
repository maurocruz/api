<?php
namespace Plinct\Api\Application\Modules\Thing;

use Plinct\Api\Application\Contracts\CreateUseCaseInterface;
use Plinct\Api\Infrastructure\FileProcessor\FileProcessor;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;

class ThingCreateUseCase implements CreateUseCaseInterface
{
	public function __construct(protected readonly DatabaseActions $databaseActions, protected readonly FileProcessor $fileProcessor)
	{
	}

	public function create(array $params = []): array
	{
		$name = $params['name'] ?? null;
		if (!$name) {
			return ['status'=> false, 'message'=> 'Name is required'];
		}
		$params['dateCreated'] = $params['dateCreated'] ?? date('Y-m-d H:i:s');
		$params['dateModified'] = $params['dateModified'] ?? date('Y-m-d H:i:s');

		$responseData = $this->databaseActions->create('thing', $params);

		if ($responseData['status'] === false) {
			return $responseData;
		} else {
			$thingData = $responseData['data'];
			return ['status'=> true, 'message'=> 'Thing item has created', 'data'=>$thingData];
		}
	}
}
