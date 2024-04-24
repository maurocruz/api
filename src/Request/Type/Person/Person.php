<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Person;

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
		$properties = $params['properties'] ?? [];
		$dataPerson = parent::getData($params);
		if (!empty($dataPerson)) {
			foreach ($dataPerson as $value) {
				$idthing = $value['idthing'];
				$idperson = $value['idperson'];
				if ($properties) {
					if (strpos($properties, 'contactPoint') !== false) $value['contactPoint'] = parent::getProperties('contactPoint', ['thing' => $idthing]);
					if (strpos($properties, 'homeLocation') !== false) $value['homeLocation'] = parent::getProperties('place', ['idplace' => $value['homeLocation'], 'properties' => 'address']);
					if (strpos($properties, 'image') !== false) $value['image'] = parent::getProperties('imageObject', ['isPartOf' => $idthing, 'orderBy'=>'position']);
					if (strpos($properties, 'memberOf') !== false) $value['memberOf'] = parent::getProperties('programMembership', ['member' => $idperson]);
				}
				$returns[] = $value;
			}
		}
		return parent::sortData($returns);
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
