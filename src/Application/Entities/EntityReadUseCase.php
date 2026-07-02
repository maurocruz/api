<?php
namespace Plinct\Api\Application\Entities;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use ReflectionException;

class EntityReadUseCase extends ThingReadUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function read(string $type, array $sanitizedParams): array
	{
		$useCaseData = $this->readEntity($type, $sanitizedParams);

		if ($useCaseData['status'] === false) {
			return $useCaseData;
		}
		if (!array_column($useCaseData['data'],'type')) {
			foreach ($useCaseData['data'] as $key => $value) {
				$useCaseData['data'][$key]['type'] = ucfirst($type);
			}
		}
		return $useCaseData;
	}
}
