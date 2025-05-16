<?php
namespace Plinct\Api\Request\Type\Person;

use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;

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
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$memberOf = $params['memberOf'] ?? null;
		$getData = new GetData('person');
		$getData->setParams($params);
		// MEMBER OF
		if ($memberOf) {
			$getData->setLeftJoin('role','role.person=person.idperson');
			$getData->setWhere("`role`.`organization`='$memberOf'");
		}
		$data = $getData->render();
		if (!empty($data) && $properties) {
			foreach ($data as $key => $item) {
				$idthing = $item['idthing'];
				$idperson = $item['idperson'];
				// ABOUT
				if (in_array('about',$properties)) {
					$data[$key]['about'] = parent::getProperties('creativeWork', ['about' => $idthing]);
				}
				// CONTACT POINT
				if (in_array('contactPoint', $properties)) {
					$data[$key]['contactPoint'] = parent::getProperties('contactPoint', ['typeHasPart'=>'Person','idHasPart' => $idthing]);
				}
				// HAS CERTIFICATION
				if (in_array('hasCertification', $properties)) {
					$data[$key]['hasCertification'] = parent::getProperties('certification', ['about' => $idthing]);
				}
				// HOME LOCATION
				if (in_array('homeLocation', $properties)) {
					$data[$key]['homeLocation'] = parent::getProperties('place', ['idplace' => $item['homeLocation'], 'properties' => 'address']);
				}
				// IMAGE
				if (in_array('image', $properties) || in_array('imageObject', $properties)) {
					$data[$key]['image'] = parent::getProperties('imageObject', ['idHasPart' => $idthing, 'orderBy' => 'position']);
				}
				// MEMBER OF
				if (in_array('memberOf', $properties)) {
					if ($memberOf) {
						$data[$key]['memberOf'] = parent::getProperties('role', ['organization' => $memberOf, 'person' => $idperson]);
					} else {
						$data[$key]['memberOf'] = parent::getProperties('role', ['person' => $idperson, 'properties'=>'memberOf']);
					}
				}
				// MAIN ENTITY OF PAGE
				if (in_array('mainEntityOfPage', $properties)) {
					$data[$key]['mainEntityOfPage'] = parent::getProperties('webPage', ['url' =>$value['mainEntityOfPage'] ?? $item['url'], 'properties' => 'image,hasPart']);
				}
			}
		}
		return parent::sortData($data);
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadedFiles
	 * @return string[]
	 */
  public function post(array $params = null, array $uploadedFiles = null): array
  {
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
