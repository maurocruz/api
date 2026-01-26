<?php
namespace Plinct\Api\Request\Type\Taxon;

use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\Relationship;
use Plinct\Api\Request\Type\Thing;

class Taxon extends Thing
{
	public function __construct(Relationship $relationship = null)
	{
		parent::__construct($relationship);
		parent::setTable('taxon');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$idHasPart = $params['idHasPart'] ?? null;
		$typeIsPartOf = $params['typeIsPartOf'] ?? null;
		$getData = new GetData('taxon');
		$getData->setParams($params);
		if ($idHasPart) {
			$getData->setParams(['thing' => $idHasPart]);
		}
		$data = $getData->render();
		// ID HAS PART
		if ($idHasPart) {
			$data[0]['subjectOf'] = parent::getHasPart($idHasPart, 'Taxon');
		}
		// PROPERTIES
		if (!empty($data) && $properties) {
			foreach ($data as $key => $value) {
				$idtaxon = $value['idtaxon'];
				$idthing = $value['thing'];
				// CHILD TAXON
				if (in_array('childTaxon', $properties)) {
					$data[$key]['childTaxon'] = parent::getProperties('taxon',['parentTaxon' => $idtaxon]);
				}
				// HAS PART
				if (in_array('hasPart', $properties)) {
					$dataHasPart = parent::getHasPart($idthing, 'Taxon', $typeIsPartOf);
					if (isset($dataHasPart[0])) {
						$data[$key]['subjectOf'] = $dataHasPart;
					}
				}
			}
		}
		return parent::sortData($data);
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadfiles
	 * @return array
	 */
	public function post(array $params = null, array $uploadfiles = null): array
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
