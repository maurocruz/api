<?php
namespace Plinct\Api\Application\Support;

use ReflectionException;

class SupportApplication
{

	/**
	 * @throws ReflectionException
	 */
	public static function hydrateOwnProperties(string $entityName, array $params): array
	{
		return (new EntityReflections($entityName))->hydrateOwnProperties($params);
	}

	/**
	 * @throws ReflectionException
	 */
	public static function hydrateFromDatabase(array $databaseData): array|object
	{
		$result = [];
		if (isset($databaseData[0])) {
			foreach ($databaseData as $value) {
				$type = $value['type'] ?? $value['@type'];
				$reflection = new EntityReflections($type);
				$result[] = $reflection->hydrateFromDatabase($value);
			}
		} elseif (isset($databaseData['type']) || isset($databaseData['@type'])) {
			$type = $databaseData['type'] ?? $databaseData['@type'];
			$reflection = new EntityReflections($type);
			return $reflection->hydrateFromDatabase($databaseData);
		}
		return $result;
	}

	/**
	 * @param bool|int $status
	 * @param string $message
	 * @param array $data
	 * @param string|null $file
	 * @return array
	 */
	public static function returnDTO(bool|int $status, string $message, array $data = [], string $file = null): array
	{
		$returns['status'] = $status;
		$returns['message'] = $message;
		$returns['count'] = count($data);
		if ($file) $returns['file'] = $file;
		$returns['data'] = $data;
		return $returns;
	}

	/**
	 * @param bool|int $status
	 * @param string $message
	 * @param array $data
	 * @param string|null $file
	 * @return string
	 */
	public static function returnDTOToJson(bool|int $status, string $message, array $data = [], string $file = null): string
	{
		return json_encode(self::returnDTO($status, $message, $data, $file), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

	/**
	 * @param string $type
	 * @param array $param
	 * @return array
	 */
	public static function sanitizeParams(string $type, array $param): array
	{
		return SanitizeParams::sanitize($type, $param);
	}
}
