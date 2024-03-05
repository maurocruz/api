<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class WebSite extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('webSite');
	}

	public function get(array $params = []): array
	{
		$returns = [];
		$data = parent::getData($params);
		if (!empty($data)) {
			foreach ($data as $item) {
				$idcretiveWork = $item['creativeWork'];
				unset($item['creativeWork']);
				$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork'=>$idcretiveWork])->ready();
				$dataWebPage = ApiFactory::request()->type('webPage')->get(['isPartOf'=>$idcretiveWork])->ready();
				$item['hasPart'] = ApiFactory::response()->type('webPage')->setData($dataWebPage)->ready();
				if (isset($dataCreativeWork[0])) {
					$returns[] = $item + $dataCreativeWork[0];
				} else {
					$returns[] = $item;
				}
			}
			return $returns;
		} else {
			return $data;
		}
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function post(array $params): array
	{
		// SAVE THING
		$dataThing = ApiFactory::request()->type('thing')->post(['type'=>'webSite'] + $params)->ready();
		if (isset($dataThing['id'])) {
			$idthing = $dataThing['id'];
			// SAVE CREATIVEWORK
			$dataCreativeWork = ApiFactory::request()->type('creativeWork')->post(['thing'=>$idthing] + $params)->ready();
			if (isset($dataCreativeWork['id'])) {
				$idcreativeWork = $dataCreativeWork['id'];
				// SAVE WEBSITE
				$dataWebSite = parent::post(['creativeWork'=>$idcreativeWork] + $params);
				// RETURN
				return ApiFactory::response()->message()->success('WebSite was created', $dataWebSite);
			}
		}
		return ApiFactory::response()->message()->fail()->generic();
	}
}
