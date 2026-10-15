<?php
namespace Plinct\Api\Support;

use Plinct\Api\Application\Support\EntityDTO;
use Plinct\Api\Domain\Entities\Entities;

class SupportController
{
	/**
	 * @param string $type
	 * @param array $params
	 * @return array
	 */
	public static function sanitizeParams(string $type, array $params): array
	{
		$newParams = [];
		foreach ($params as $key => $value) {
			if ($value === null) {
				continue;
			}
			// PEGA A CLASSE DO TIPO
			$entities =  new Entities();
			$entity = $entities->getEntity($type);

			// ELIMINA ESPAÇOS EM BRANCO
			$value = trim($value," \t\n\r\0\x0B");

			// SÓ WEB PAGE ELEMENT NA PROPRIEDADE TEXT ACEITA TAGS
			if ($key !== 'text' && $key !== 'description' && $key !== 'where') {
				$value = htmlspecialchars(strip_tags($value));
			}

			// CONVERTE A PROPRIEDADE 'properties' NUM ARRAY
			if ($key === 'properties') {
				$value = array_map('trim', explode(',',$value));
			}

			// FIELDS MANDATORIES
			if ($key === 'fields') {
				$valueArray = explode(',',$value);
				// adiciona igthing
				if (!in_array('idthing', $valueArray) && property_exists($entity, 'idthing')) {
					$valueArray[] = 'idthing';
				}
				// adiciona type
				if (!in_array('type',$valueArray) && property_exists($entity, 'type')) {
					$valueArray[] = 'type';
				}
				$value = implode(',',$valueArray);
			}

			// CONSTRÓI ARRAY
			$newParams[$key] = $value;
		}
		return $newParams;
	}

	/**
	 * @param array $databaseData
	 * @param string|null $message
	 * @return array
	 */
	public static function returnDTO(array $databaseData, string $message = null): array
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
		foreach ($data as $key => $value) {
			$type = $value['type'] ?? $value['@type'];
			$objectType = Entities::getObjectByClassName($type, $value);
			$data[$key] = $objectType->jsonSerialize();
		}
		$entityDTO->setData($data);
		return $entityDTO->ready();
	}
}
