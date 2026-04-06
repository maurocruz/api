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
	public static function hydrateFromDatabase(array $databaseData): array
	{
		$result = [];
		foreach ($databaseData as $value) {
			$type = $value['type'];
			$reflection = new EntityReflections($type);
			$result[] = $reflection->hydrateFromDatabase($value);
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

	public static function returnDTO(bool|int $status, string $message, array $data = [], string $file = null): array
	{
		$returns['status'] = $status;
		$returns['message'] = $message;
		if ($file) $returns['file'] = $file;
		$returns['data'] = $data;
		return $returns;
	}

	public static function returnDTOToJson(bool|int $status, string $message, array $data = [], string $file = null): string
	{
		return json_encode(self::returnDTO($status, $message, $data, $file), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

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

		// STATUS
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

		// DATA
			// HIDRATA DADOS
			//$entities = SupportApplication::hydrateFromDatabase($data);
			//$schemaOrgFormat = SupportApplication::convertFromObjectToSchemaOrgArrayDTO($entities);
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
		return SupportApplication::convertFromObjectToSchemaOrgArrayDTO($entities);
	}
}
