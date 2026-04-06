<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

class CreativeWorkReadUseCase extends ThingReadUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function read(array $queryParams = []): array
	{
		$sanitizedParams = SupportApplication::sanitizeParams('CreativeWork', $queryParams);

		$useCaseData = $this->readEntity('CreativeWork', $sanitizedParams);

		if ($useCaseData['status'] === false) {
			return $useCaseData;
		}
		return SupportApplication::returnDTOFromDatabaseData($useCaseData,  'CreativeWork found');
	}
}
