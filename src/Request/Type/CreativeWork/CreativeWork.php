<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\HttpRequestInterface;

class CreativeWork extends Entity implements HttpRequestInterface
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('creativeWork');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$returns = [];
		$properties = $params['properties'] ?? null;
		$dataCreativeWork = parent::getData($params);
		if (isset($dataCreativeWork['error'])) {
			return  ApiFactory::response()->message()->error()->anErrorHasOcurred($dataCreativeWork);
		} elseif (!empty($dataCreativeWork) && $properties) {
			foreach ($dataCreativeWork as $creativeWork) {
				$author = $creativeWork['author'];
				if (strpos($properties, 'author') !== false) $creativeWork['author'] = parent::getProperties('person', ['idperson' => $author, 'properties' => 'image']);
				$returns[] = $creativeWork;
			}
		} else {
			$returns = $dataCreativeWork;
		}
		/*else{
			$newData = [];
			foreach ($data as $item) {
				if (isset($item['type'])) {
					$table = lcfirst($item['type']);
					$thing = $item['thing'];
					$dataType = ApiFactory::server()->connectBd($table)->read(['where' => "`thing`=$thing"]);
					if (isset($dataType[0])) {
						$newData[] = $item + $dataType[0];
					} else {
						$newData[] = $item;
					}
				} else {
					$newData[] = $item;
				}
			}
		}*/

		return parent::sortData($returns);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$params['type'][] = 'CreativeWork';
		if(isset($params['isPartOf']) && $params['isPartOf'] === '') {
			unset($params['isPartOf']);
		}
		return parent::createWithParent('thing', $params);
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
		$idcreativeWork = $params['idcreativeWork'] ?? $params['creativeWork'] ?? null;
		if ($idcreativeWork) {
			$dataCreativeWork = parent::getData(['idcreativeWork'=>$idcreativeWork]);
			if (!empty($dataCreativeWork)) {
				$idthing = $dataCreativeWork[0]['thing'];
				return ApiFactory::request()->type('thing')->delete(['idthing'=>$idthing])->ready();
			}else {
				return ApiFactory::response()->message()->fail()->generic($params,'CreativeWork id not found');
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idcreativeWork or creativeWork"]);
		}
	}
}
