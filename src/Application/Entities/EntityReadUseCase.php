<?php
namespace Plinct\Api\Application\Entities;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

class EntityReadUseCase extends ThingReadUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function read(string $type, array $params = []): array
	{
		$sanitizedParams = SupportApplication::sanitizeParams($type, $params);

		$useCaseData = $this->readEntity($type, $sanitizedParams);

		if ($useCaseData['status'] === false) {
			return $useCaseData;
		}
		return SupportApplication::returnDTOFromDatabaseData($useCaseData,  $type.' '. (empty($useCaseData['data']) ? 'not ': '') .'found');
	}
}
