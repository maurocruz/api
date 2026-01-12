<?php
namespace Plinct\Api\Request\Type\Action;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Type\Thing;

class Action extends Thing
{
	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
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
				// OBJECT
				if (in_array('object',$properties)) {
					$object = $value['object'];
					$dataObject = ApiFactory::request()->type('thing')->get(['idthing'=>$object,'hasPart'=>''])->ready();
					$value['object'] = isset($dataObject[0]) ? ApiFactory::response()->type('thing')->setData($dataObject[0])->ready() : null;
				}
				$data[$key] = $value;
			}
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
		$agent = $params['agent'] ?? null;
		$object = $params['object'] ?? null;
		$provider = $params['provider'] ?? null;
		if ($agent == '') unset($params['agent']);
		if ($object == '') unset($params['object']);
		if ($provider == '') unset($params['provider']);
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
		return parent::erase('thing',$params);
	}
}
