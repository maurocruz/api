<?php
declare(strict_types=1);
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
		$data = $this->getData($params);
		if (isset($data['error'])) {
			return  ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		} else {
			//var_dump($data);
		}
		return $this->sortData($data);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$params['type'][] = 'Action';
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
		return parent::erase('thing', $params);
	}
}
