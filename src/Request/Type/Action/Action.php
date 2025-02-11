<?php
namespace Plinct\Api\Request\Type\Action;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\HttpRequestInterface;

class Action extends Entity implements HttpRequestInterface
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('action');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = parent::propertiesToArray($params['properties'] ?? null);
		$data = $this->getData($params);
		if ($properties) {
			foreach ($data as $key => $value) {
				// AGENT
				if (in_array('agent',$properties)) {
					$agent = $value['agent'];
					$dataAgent = ApiFactory::request()->type('user')->get(['iduser'=>$agent])->ready();
					$value['agent'] = isset($dataAgent[0]) ? ApiFactory::response()->type('thing')->setData($dataAgent[0])->ready() : null;
				}
				$data[$key] = $value;
			}
		}
		return $this->sortData($data);
	}
}
