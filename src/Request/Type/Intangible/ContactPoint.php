<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;
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
		$hasPart = $params['hasPart'] ?? null;
		if ($hasPart !== null) {
			$sqlQuery = "SELECT * FROM `contactPoint` 
JOIN `thing` ON `thing`.idthing = `contactPoint`.thing
JOIN `thing_has_thing` ON `thing_has_thing`.idIsPartOf = `thing`.idthing
WHERE `thing_has_thing`.idHasPart = '{$hasPart}' AND `thing_has_thing`.typeIsPartOf = 'ContactPoint'
ORDER BY `thing_has_thing`.position;
";
			$data = PDOConnect::run($sqlQuery);
		} else {
			$data = parent::getData($params, true);
		}
		$returns = ApiFactory::response()->type('ContactPoint')->setData($data)->setParams($params)->ready();
		return $this->sortData($returns);
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
