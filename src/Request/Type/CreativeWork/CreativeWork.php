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
		$data = parent::getData($params);
		if (!empty($data)) {
			foreach ($data as $item) {
				$idthing = $item['thing'];
				$dataThing = ApiFactory::request()->type('thing')->get(['idthing' => $idthing])->ready();
				$returns[] = $item + $dataThing[0];
			}
		}
		return parent::array_sort($returns, $params);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$params['type'] = 'CreativeWork';
		return parent::create('thing', $params);
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
