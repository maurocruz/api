<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\Relationship;

class Role extends Entity
{
	public function __construct(Relationship $relationship = null)
	{
		parent::__construct($relationship);
		$this->setTable('role');
	}

	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('role');
		$getData->setParams($params);

		$data = $getData->render();
		if (!empty($data) && $properties) {
			foreach ($data as $key => $value) {
				// MEMBER
				if (in_array('member', $properties) || in_array('person', $properties)) {
					$personParams['idperson'] = $value['person'];
					if (in_array('memberOf', $properties)) {
						$personParams['properties'] = 'memberOf';
					}
					if (in_array('image', $properties)){
						$personParams['properties'] .= ',image';
					}
					$dataMember = parent::getProperties('person', $personParams);
					if (isset($dataMember[0])) {
						$data[$key]['member'] = ApiFactory::response()->type('person')->setData($dataMember[0])->ready();
					}
				}
				// MEMBER OF
				if (in_array('memberOf', $properties) || in_array('organization', $properties)) {
					$dataMemberOf = parent::getProperties('organization', ['idorganization' => $value['organization']]);
					if (isset($dataMemberOf[0])) {
						$data[$key]['memberOf'] = ApiFactory::response()->type('organization')->setData($dataMemberOf[0])->ready();
					}
				}
				unset($data[$key]['person']);
				unset($data[$key]['organization']);
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
		$name = $params['name'] ?? null;
		$person = $params['person'] ?? null;
		$organization = $params['organization'] ?? null;

		if ($name && $person && $organization) {
			$params['dateCreated'] = date("Y-m-d H:i:s");
			return parent::createWithParent('thing', $params);
		} else {
			return ApiFactory::response()->message()->fail()->generic(["Missing mandatory data: name, person and organization"]);
		}
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
		$idrole = $params['idrole'] ?? null;
		if ($idrole !== null) {
			return parent::erase('thing', ['idrole'=>$idrole]);
		} else {
			return ApiFactory::response()->message()->fail()->generic(['Role not found']);
		}
	}

}
