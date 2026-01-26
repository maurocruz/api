<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\Relationship;
use Plinct\Api\Request\Type\Thing;

class ContactPoint extends Thing
{
	/**
	 *
	 */
	public function __construct(Relationship $relationship = null)
	{
		parent::__construct($relationship);
		$this->setTable('contactPoint');
		$this->setType("ContactPoint");
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$idHasPart = $params['idHasPart'] ?? null;
		if ($idHasPart) {
			$dataGet = new GetData('thing_has_thing');
			$dataGet->setLeftJoin('thing','`thing`.idthing=`thing_has_thing`.idIsPartOf');
			$dataGet->setLeftJoin('contactPoint','`contactPoint`.thing=`thing`.idthing');
			$dataGet->setParams($params + ['limit'=>'none','typeIsPartOf'=>'ContactPoint']);
		} else {
			$dataGet = new GetData('contactPoint');
			$dataGet->setParams($params);
		}
		$data = $dataGet->render();
		foreach ($data as $key => $value) {
			$position = $value['position'] ?? null;
			if ($position) {
				$data[$key]['identifier'][] = ['@type' => 'PropertyValue', 'name' => 'position', 'value' => $position];
			}
			unset($data[$key]['position']);
			unset($data[$key]['typeHasPart']);
			unset($data[$key]['typeIsPartOf']);
		}
		return $this->sortData($data);
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadfiles
	 * @return array
	 */
	public function post(array $params = null, array $uploadfiles = null): array
	{
		$idHasPart = $params['idHasPart'] ?? null;
		$typeHasPart = $params['typeHasPart'] ?? null;
		$contactType = $params['contactType'] ?? null;
		$telephone = $params['telephone'] ?? null;
		$email = $params['email'] ?? null;
		if (!!$params['name']) {
			$name = $params['name'];
		} else if (!!$contactType) {
			$name = $contactType;
		} elseif (!!$telephone) {
			$name = $telephone;
		} elseif (!!$email) {
			$name = $email;
		} else {
			$name = null;
		}
		if(!!$name && !!$idHasPart && !!$typeHasPart && (!!$telephone || !!$email)) {
			// insert data in contatc point and return new idthing
			$params['name'] = $name;
			$dataNewContactPoint = $this->createWithParent('thing', $params);

			// IF IS RELATIONSHIP
			if (isset($dataNewContactPoint['status']) && $dataNewContactPoint['status'] === "success") {
				$message = $dataNewContactPoint['message'] ?? null;
				$value = $dataNewContactPoint['data'][0];
				$idthing = $value['idthing'];
				// insert row in relationship thing_has_thing
				$returns = parent::createRelationShip($idHasPart, $typeHasPart, $idthing, "ContactPoint");
				if (isset($returns['status']) && $returns['status'] == "success") {
					//return $dataNewContactPoint;
					$returns['message'] = $message." and ".$returns['message'];
					$returns['data'][] = ApiFactory::response()->type('ContactPoint')->setData($value)->ready();
					return $returns;
				} else {
					return ApiFactory::response()->message()->fail()->generic($returns);
				}
			}

			else {
				return ApiFactory::response()->message()->fail()->generic($dataNewContactPoint);
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: name, idHasPart, typeHasPart and telephone or email']);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		$idcontactPoint = $params['idcontactPoint'] ?? null;
		$typeHasPart = $params['typeHasPart'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		$idIsPartOf = $params['idIsPartOf'] ?? null;
		$position = $params['position'] ?? null;
		if ($idcontactPoint !== null) {
			if ($position && $typeHasPart && $idHasPart && $idIsPartOf) {
				$relationship = new Relationship();
				$relationship->setTypeHasPart($typeHasPart);
				$relationship->setIdHasPart($idHasPart);
				$relationship->setTypeIsPartOf('ContactPoint');
				$relationship->setIdIsPartOf($idIsPartOf);
				$relationship->put(['position'=>$position]);
			}
			return parent::update('thing', $params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: idcontactPoint']);
		}
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		$idcontactPoint = $params['idcontactPoint'] ?? null;
		if ($idcontactPoint !== null) {
			return parent::erase('thing', ['idcontactPoint'=>$idcontactPoint]);
		} else {
			return ApiFactory::response()->message()->fail()->generic(['Contact Point not found']);
		}
	}
}
