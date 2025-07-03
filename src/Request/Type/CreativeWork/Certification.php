<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\HttpRequestInterface;

class Certification extends Entity implements HttpRequestInterface
{

	public function __construct()
	{
		$this->setTable('certification');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('certification');
		$getData->setLeftJoin('creativeWork','creativeWork.idcreativeWork=certification.creativeWork');
		$getData->setParams($params);
		$data = $getData->render();
		if (!empty($data) && $properties) {
			foreach ($data as $key => $value) {
				$about = $value['about'];
				$issuedBy = $value['issuedBy'];
				// ABOUT
				if (in_array('about', $properties)) {
					$dataAbout = parent::getProperties('thing', ['idthing' => $about]);
					if (isset($dataAbout[0])) {
						$data[$key]['about'] = $dataAbout[0];
					}
				}
				// ISSUED BY
				if (in_array('issuedBy', $properties)) {
					$data[$key]['issuedBy'] = parent::getProperties('organization', ['idorganization' => $issuedBy])[0];
				}
			}
		}
		return parent::sortData($data);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$name = $params['name'] ?? null;
		$issuedBy = $params['issuedBy'] ?? null;
		if ($issuedBy && $name) {
			// SAVE CREATIVEWORK
			return $this->createWithParent('creativeWork', $params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: name']);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::update('creativeWork', $params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('creativeWork', $params);
	}
}
