<?php
namespace Plinct\Api\Application\Entities\Thing;

use Plinct\Api\Domain\Entities\Entities;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

class ThingReadUseCase
{
	public function __construct(
		protected readonly DatabaseActions $databaseActions
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function readEntity(string $type, array $sanitizedParams = []): array
	{
		// VERIFICA SE EXISTE ENTIDADE PARA TYPE
		if (!Entities::hasEntity($type)) {
			return ['status' => false, 'message' => 'Entity not found', 'data' => []];
		}

		// OBTÉM RELACIONAMENTOS
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		$idIsPartOf = $sanitizedParams['idIsPartOf'] ?? null;
		if ($idHasPart && $idIsPartOf) {
			return self::readRelationshipHasPart($idHasPart, $idIsPartOf, $sanitizedParams);
		}

		// BUSCA POR IS PART OF (RELATIONSHIP)
		$isPartOf = $sanitizedParams['isPartOf'] ?? $sanitizedParams['idHasPart'] ?? null;
		if ($isPartOf) {
			return self::readRelationshipIsPartOf($isPartOf, $type, $sanitizedParams);
		}

		// BUSCA POR PARAMS
		$databaseData = $this->databaseActions->read(lcfirst($type), $sanitizedParams)->render();
		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		$data = $databaseData['data'];

		// HAS PART IS TRUE
		if (isset($sanitizedParams['hasPart'])) {
			foreach ($data as $key => $value) {
				$type = $value['type'];
				$thing = $value['idthing'];
				$databaseDataPart = $this->databaseActions->read(lcfirst($type), ['thing' => $thing])->render();
				if ($databaseDataPart['status'] === false) {
					return $databaseDataPart;
				}
				$databaseData['data'][$key] = $databaseDataPart['data'][0];
			}
		}
		return $databaseData;
	}

	/**
	 * @throws ReflectionException
	 */
	private function readRelationshipHasPart(string $idHasPart, string $idIsPartOf, array $addtionalparams = []): array
	{
		$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart, 'idIsPartOf' => $idIsPartOf]);
		$getData->setParams($addtionalparams);
		$databaseData = $getData->render();
		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		$data = $databaseData['data'];
		if (empty($data)) {
			return ['status'=> true, 'message'=> 'is part of not found', 'data'=>[]];
		}
		// PEGA IDISPARTOF E TYPEISPARTOF
		$item = $data[0];
		$typeIsPartOf = $item['typeIsPartOf'];
		$idIsPartOf = $item['idIsPartOf'];
		// OBTÉM DADOS DO TYPEHASPART
		return $this->databaseActions->read(lcfirst($typeIsPartOf), ['thing' => $idIsPartOf])->render();
	}

	/**
	 * @throws ReflectionException
	 */
	private function readRelationshipIsPartOf(string $idHasPart, string $typeHasPart, array $addtionalparams = []): array
	{
		$addtionalparams = array_merge(['orderBy'=>'position', 'ordering'=>'desc'], $addtionalparams);
		$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart, 'typeIsPartOf' => ucfirst($typeHasPart)]);
		$getData->setParams($addtionalparams);
		$getData->setJoin('thing', 'thing_has_thing.idIsPartOf = thing.idthing', 'thing');
		$responseData = $getData->render();

		if ($responseData['status'] === false) {
			return $responseData;
		}
		$data = $responseData['data'];

		if (empty($data)) {
			return ['status'=> true, 'message'=> $typeHasPart . ' not found', 'data'=>[]];
		}

		// RETORNA ID DOS THINGS
		$things = [];
		foreach ($data as $value) {
			$things[] = $value['idIsPartOf'];
		}
		// OBTÉM DADOS DO TYPEHASPART
		return $this->databaseActions->read(lcfirst($typeHasPart), ['thing' => implode('|',$things)])->render();
	}

	/**
	 * @throws ReflectionException
	 */
	protected function getHasPart(string $idIsPartOf, string $typeIsPartOf, string $typeHasPart = null, array $params = []): array
	{
		$getData = $this->databaseActions->read('thing_has_thing', ['idIsPartOf' => $idIsPartOf, 'typeIsPartOf' => ucfirst($typeIsPartOf)]);
		$getData->setJoin('thing', 'thing_has_thing.idHasPart = thing.idthing', 'thing');
		$getData->setParams($params);
		$responseData = $getData->render();

		if ($responseData['status'] === false) {
			return $responseData;
		}
		$data = $responseData['data'];

		if (empty($data)) {
			return ['status'=> true, 'message'=> $typeHasPart . ' not found', 'data'=>[]];
		}

		// RETORNA ID DOS THINGS

		$things = [];
		foreach ($data as $value) {
			$things[] = $value['idHasPart'];
		}
		// OBTÉM DADOS DO TYPEHASPART
		return  $this->databaseActions->read(lcfirst($typeHasPart), ['thing' => implode('|',$things)])->render();
	}

	/**
	 * @throws ReflectionException
	 */
	protected function getIsPartOf(string $type, int $idthing): ?array
	{
		$paramsHasPart['idHasPart'] = $idthing;
		$paramsHasPart['typeHasPart'] = $type;
		$paramsHasPart['orderBy'] = $sanitizedParams['orderBy'] ?? 'thing.name';
		$dataGetHasPart = $this->databaseActions->read('thing_has_thing', $paramsHasPart);
		$dataGetHasPart->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf');
		$dataHasPart = $dataGetHasPart->render();
		if ($dataHasPart['status'] === false) {
			return $dataHasPart;
		}
		foreach ($dataHasPart['data'] as $key2 => $value) {
			$type = $value['type'];
			$thing = $value['idthing'];
			$dataThing = $this->databaseActions->read(lcfirst($type), ['thing' => $thing])->render();
			if ($dataThing['status'] === false) {
				return $dataThing;
			}
			$dataHasPart['data'][$key2] = array_merge($dataHasPart['data'][$key2], $dataThing['data'][0]);
		}
		return $dataHasPart['data'] ?? null;
	}

}
