<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class ProgramMembership extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('programMembership');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$returns = [];
		$properties = $params['properties'] ?? null;
		$data = parent::getData($params);
		if (!empty($data)) {
			foreach ($data as $item) {
				$member = $item['member'];
				if ($properties) {
					if (strpos($properties, 'member') !== false) $item['member'] = parent::getProperties('person', ['idperson' => $member]);
				}
				$returns[] = $item;
			}
		}
		return parent::sortData($returns);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$name = $params['programName'] ?? $params['name'] ?? null;
		$hostingOrganization = $params['hostingOrganization'] ?? null;
		$member = $params['member'] ?? null;
		if ($name && $hostingOrganization && $member) {
			$params['name'] = $name;
			$params['type'][] = "ProgramMembership";
			$dataProgramMembership = parent::createWithParent('thing', $params);
			if (isset($dataProgramMembership[0])) {
				$idprogramMembership = $dataProgramMembership[0]['idprogramMembership'];
				$getProgramMembership = ApiFactory::request()->type('programMembership')->get(['idprogramMembership'=>$idprogramMembership])->ready();
				if (!empty($getProgramMembership)) {
					return ApiFactory::response()->type('programMembership')->setData($getProgramMembership)->ready();
				} else {
					return ApiFactory::response()->message()->fail()->generic($getProgramMembership);
				}
			} else {
				return ApiFactory::response()->message()->fail()->generic($dataProgramMembership);
			}
		} else {
			return ApiFactory::response()->message()->fail()->generic(["Missing mandatory data: name, hostingOrganization and member"]);
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