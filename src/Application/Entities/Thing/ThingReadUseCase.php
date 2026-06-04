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
			return self::readRelationship($idHasPart, $idIsPartOf, $sanitizedParams);
		}

		// BUSCA POR IS PART OF (RELATIONSHIP)
		if ($idIsPartOf) {
			return self::readRelationshipIsPartOf($idIsPartOf, $type, $sanitizedParams);
		}

		// BUSCA POR HAS PART (RELATIONSHIP)
		if ($idHasPart) {
			return self::readRelationshipHasPart($idHasPart, $type, $sanitizedParams);
		}

		// BUSCA POR PARAMS
		$getData = $this->databaseActions->read(lcfirst($type), $sanitizedParams);
		$databaseData = $getData->render();

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
	private function readRelationship(string $idHasPart, string $idIsPartOf, array $addtionalparams = []): array
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
		$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart, 'typeHasPart' => ucfirst($typeHasPart)]);
		$getData->setParams($addtionalparams);
		$getData->setJoin('thing', 'thing_has_thing.idHasPart = thing.idthing', 'thing');
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
	private function readRelationshipHasPart(string $idHasPart, string $typeIsPartOf, array $addtionalparams = []): array
	{
		$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart]);
		$getData->setParams($addtionalparams);
		$getData->setJoin('thing', 'thing_has_thing.idIsPartOf = thing.idthing', 'thing');
		$responseData = $getData->render();
		if ($responseData['status'] === false) {
			return $responseData;
		}
		$data = $responseData['data'];

		if (empty($data)) {
			return ['status'=> true, 'message'=> $typeIsPartOf . ' not found', 'data'=>[]];
		}
		return ['status'=> true, 'message'=> $typeIsPartOf . ' found', 'data'=> $data];
	}

	/**
	 * @throws ReflectionException
	 */
	protected function getHasPart(string $idIsPartOf, string $typeIsPartOf = null, string $typeHasPart = null, array $params = []): array
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
	protected function getPropertiesValue(string $idthing): array
	{
		return $this->databaseActions->read('thing_has_thing',['idHasPart'=>$idthing,'typeIsPartOf'=>'PropertyValue','fields'=>'name,value,type as `@type`'])
			->setJoin('thing', 'thing_has_thing.idIsPartOf = thing.idthing')
			->setJoin('propertyValue', 'thing_has_thing.idIsPartOf = propertyValue.thing')
			->render();
	}
}
