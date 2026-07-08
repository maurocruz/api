<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\Collection;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkCreateUseCase;

class CollectionCreateUseCase extends CreativeWorkCreateUseCase
{
	public function create(array $sanitizedParams): array
	{
		$sanitizedParams['type'] = 'Collection';

		$dataCreativeWork = parent::create($sanitizedParams);
		if($dataCreativeWork['status'] === false) {
			return $dataCreativeWork;
		}
		if ($dataCreativeWork['message'] === 'Relationship created successfully') {
			return $dataCreativeWork;
		}
		$valueCreativeWork = $dataCreativeWork['data'][0];
		$thing = $valueCreativeWork['thing'];
		$creativeWork = $valueCreativeWork['idcreativeWork'];

		return $this->databaseActions->create('collection', ['thing' => $thing, 'creativeWork' => $creativeWork]);
	}
}
