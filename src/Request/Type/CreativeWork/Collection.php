<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Exception;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\HttpRequestInterface;
use Plinct\Api\Request\Server\Relationship;

class Collection extends CreativeWork implements HttpRequestInterface
{
	/**
	 *
	 */
	public function __construct(Relationship $relationship = null)
	{
		parent::__construct($relationship);
		$this->setTable('collection');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$isPartOf = $params['isPartOf'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		$fields = $params['fields'] ?? null;
		unset($params['isPartOf']);
		if ($idHasPart && $fields === 'count(idHasPart)') {
			$getData = new GetData('thing_has_thing', false);
			$getData->setFields($fields);
			$getData->setParams($params);
		} else {
			$getData = new GetData('collection');
			$getData->setParams($params);
			$getData->setLeftJoin('creativeWork', 'creativeWork.idcreativeWork=collection.creativeWork');
			// if NOT EXISTS IS PART OF
			if ($isPartOf) {
				$getData->setJoin('thing_has_thing', 't1.idHasPart=collection.thing', 't1');
				$getData->setWhere('not exists (select 1 from thing_has_thing as t2 where t1.idHasPart=t2.idIsPartOf)');
				$getData->setParams(['groupBy' => 'idcollection', 'limit' => 'none']);
			}
		}
		$data = $getData->render();
		if ($properties) {
			foreach ($data as $key => $value) {
				$idthing = $value['thing'];
				// HAS PART
				if (in_array('hasPart', $properties)) {
					$data[$key]['hasPart'] = parent::getHasPart($idthing, 'Collection');
				}
				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$data[$key]['isPartOf'] = parent::getIsPartOf($idthing,'Collection');
				}
			}
		}
		return parent::sortData($data);
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadfiles
	 * @return array
	 * @throws Exception
	 */
	public function post(array $params = null, array $uploadfiles = null): array
	{
		$idHasPart = $params['idHasPart'] ?? null;
		$idIsPartOf = $params['idIsPartOf'] ?? null;
		$typeIsPartOf = $params['typeIsPartOf'] ?? null;
		if ($idHasPart) { // added relationships
			$returns = [];
			if(!empty($uploadfiles)) {
				$params['typeHasPart'] = 'Collection';
				$returns[] = parent::uploadfiles($params, $uploadfiles);
			} elseif ($idIsPartOf && $typeIsPartOf) {
				$returns[] = self::createRelationShip($idHasPart, "Collection", $idIsPartOf, ucfirst($typeIsPartOf));
			}
			return $returns;
		} else { // add new
			return parent::createWithParent('creativeWork', $params);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::update('creativeWork', $params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('creativeWork', $params);
	}
}
