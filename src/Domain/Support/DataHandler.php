<?php
namespace Plinct\Api\Domain\Support;

use ReflectionException;

class DataHandler
{
	/**
	 * @throws ReflectionException
	 */
	public static function payloadBuilder(string $className, array $params): array
	{
		// HYDRATATION
		$hydrated = EntityHydrator::fromArray($className, $params);

		// FILTER
		return PayloadBuilder::build($hydrated);
	}
}
