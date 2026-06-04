<?php
namespace Plinct\Api\Application\Support;

use Plinct\Api\Domain\Contracts\EntityInterface;
use Plinct\Api\Domain\Entities\Entities;
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
		} elseif (isset($databaseData['type'])) {
			$type = $databaseData['type'];
			$reflection = new EntityReflections($type);
			return $reflection->hydrateFromDatabase($databaseData);
		}
		return $result;
	}

	/**
	 * @throws ReflectionException
	 */
	public static function convertFromObjectToSchemaOrgArrayDTO(array $entities): array
	{
		return EntityReflections::convertFromObjectToSchemaOrgArrayDTO($entities);
	}

	/**
	 * @param string $name
	 * @return EntityInterface|null
	 */
	public static function getEntity(string $name): ?EntityInterface
	{
		return Entities::getEntity($name);
	}

	/**
	 * @param string|null $properties
	 * @return string[]|null
	 */
	public static function propertiesToArray(string $properties = null): ?array
	{
		if (!$properties) return [];
		$propertiesArray = explode(',',$properties);
		array_walk($propertiesArray, function (&$value) {$value = trim($value);});
		return $propertiesArray;
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

	/**
	 * @throws ReflectionException
	 */
	public static function returnDTOFromDatabaseData(array $databaseData, string $message = null): array
	{
		$status = $databaseData['status'] ?? false;
		if ($status === false) {
			return $databaseData;
		}
		$data = $databaseData['data'] ?? [];

		$entityDTO = new EntityDTO();
		$entityDTO->setStatus($status);

		// MENSAGEM
		$message = $message ?? $databaseData['message'] ?? '';
		$entityDTO->setMessage($message);

		// COUNT
			// VERIFICAR SE EXISTE UM COUNT
			$padrao = '/^count\([a-zA-Z0-9_\s]+\)$/';
			$countFields = null;
			foreach ($data as $valor) {
				foreach ($valor as $key2 => $valor2) {
					if (preg_match($padrao, $key2)) {
						$countFields = $valor2;
					}
				}
			}
		$count = $countFields ?? count($data) ?? 'nd';
		$entityDTO->setCount($count);

		// HIDRATA DADOS
		$schemaOrgFormat = self::convertDataToSchemaOrg($data);
		$entityDTO->setData($schemaOrgFormat);
		return $entityDTO->ready();
	}

	/**
	 * @throws ReflectionException
	 */
	public static function convertDataToSchemaOrg(array $data): array
	{
		if (isset($data['data'])) return [];
		$entities = SupportApplication::hydrateFromDatabase($data);
		return EntityReflections::convertFromObjectToSchemaOrgArrayDTO($entities);
	}
}
