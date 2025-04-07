<?php
namespace Plinct\Api\Request\Type\Taxon;

use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;

class Taxon extends Entity
{
	public function __construct()
	{
		parent::setTable('taxon');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('taxon');
		$getData->setParams($params);
		$data = $getData->render();
		if (!empty($data) && $properties) {
			foreach ($data as $key => $value) {
				$idtaxon = $value['idtaxon'];
				$idthing = $value['thing'];
				// CHILD TAXON
				if (in_array('childTaxon', $properties)) {
					$data[$key]['childTaxon'] = parent::getProperties('taxon',['parentTaxon' => $idtaxon]);
				}
				if (in_array('image', $properties)) {
					$data[$key]['image'] = parent::getProperties('imageObject', ['idHasPart' => $idthing, 'orderBy' => 'position']);
				}
			}
		}
		return $this->sortData($data);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		return parent::createWithParent('thing',$params);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::update('thing',$params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('thing',$params);
	}
}
