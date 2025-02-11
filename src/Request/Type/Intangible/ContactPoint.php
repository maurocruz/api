<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Tool\TypeBuilder;

class ContactPoint extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('contactPoint');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$typeHasPart = $params['typeHasPart'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		if ($typeHasPart !== null && $idHasPart !== null) {
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
			unset($data[$key]['position']);
			unset($data[$key]['typeHasPart']);
			unset($data[$key]['typeIsPartOf']);
		}
		return $this->sortData($data);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$idHasPart = $params['idHasPart'] ?? null;
		$typeHasPart = $params['typeHasPart'] ?? null;
		$telephone = $params['telephone'] ?? null;
		$email = $params['email'] ?? null;
		$name = $params['name'] ?? $params['contactType'] ?? $telephone ?? $email ?? null;
		if($name && $idHasPart && $typeHasPart && ($telephone || $email)) {
			// insert data in contatc point and return new idthing
			$dataNewContactPoint = $this->createWithParent('thing', $params);
			if (isset($dataNewContactPoint[0])) {
				$value = $dataNewContactPoint[0];
				$typeBuilder = new TypeBuilder($value);
				$idcontactPoint = $typeBuilder->getId();
				$idthing = $typeBuilder->getPropertyValue('idthing');
				// insert row in relationship thing_has_thing
				$returns = parent::createRelationShip($idHasPart, $typeHasPart, $idthing, "ContactPoint");
				if (empty($returns)) {
					return $this->get(['idcontactPoint' => $idcontactPoint]);
				} else {
					return ApiFactory::response()->message()->fail()->generic($returns);
				}
			} else {
				return ApiFactory::response()->message()->fail()->generic($dataNewContactPoint);
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: name, idHasPartOf, typeHasPartOf and telephone or email']);
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
		return parent::erase('thing', $params);
	}
}
