<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Person;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Person extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('person');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$returns = [];
		$properties = $params['properties'] ?? null;
		$memberOf = $params['memberOf'] ?? null;
		if ($memberOf !== null) {
			$dataProgramMembership = ApiFactory::request()->type('programMembership')->get($params)->ready();
			if(!empty($dataProgramMembership)) {
				foreach($dataProgramMembership as $programMembership) {
					$member = $programMembership['member'];
					$dataPerson = parent::getData(['idperson'=>$member,'properties'=>'image'] + $params);
					if (!empty($dataPerson)) {
						$valuePerson = $dataPerson[0];
						$valuePerson['memberOf'] = $programMembership;
						$returns[] = $valuePerson;
					}
				}
				$returns = $this->getProps($returns, $properties);
			}
		} else {
			$dataPerson = parent::getData($params);
			$returns = $this->getProps($dataPerson, $properties);
		}
		return parent::sortData($returns);
	}

	private function getProps(array $dataPerson, $properties): array
	{
		$returns = [];
		if (!empty($dataPerson) && $properties) {
			foreach ($dataPerson as $value) {
				$idthing = $value['idthing'];
				$idperson = $value['idperson'];
				if (strpos($properties, 'contactPoint') !== false) $value['contactPoint'] = parent::getProperties('contactPoint', ['thing' => $idthing]);
				if (strpos($properties, 'homeLocation') !== false) $value['homeLocation'] = parent::getProperties('place', ['idplace' => $value['homeLocation'], 'properties' => 'address']);
				if (strpos($properties, 'image') !== false) $value['image'] = parent::getProperties('imageObject', ['isPartOf' => $idthing, 'orderBy' => 'position']);
				if (strpos($properties, 'memberOf') !== false) $value['memberOf'] = parent::getProperties('programMembership', ['member' => $idperson]);
				$returns[] = $value;
			}
		}
		return $returns;
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadedFiles
	 * @return string[]
	 */
  public function post(array $params = null, array $uploadedFiles = null): array
  {
	  $params['type'][] = 'Person';
		return parent::createWithParent('thing', $params, $uploadedFiles);
  }

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::update('thing', $params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('thing', $params);
	}
}
