<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\HttpRequestInterface;
use Plinct\Api\Request\Type\Thing;

class CreativeWork extends Thing implements HttpRequestInterface
{
	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTable('creativeWork');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('creativeWork');
		$getData->setParams($params);
		$data = $getData->render();

		if (!empty($data)) {
			foreach ($data as $key => $value) {
				$idcreativeWork = $value['idcreativeWork'];
				$type = $value['type'];
				if ($type !== 'CreativeWork' && $type !== 'Thing') {
					$getDataCreativeWork = new GetData(lcfirst($type));
					$getDataCreativeWork->setParams(['creativeWork'=>$idcreativeWork]);
					$dataCreativeWork = $getDataCreativeWork->render();
					if (isset($dataCreativeWork[0])) {
						$data[$key] = $dataCreativeWork[0] + $value;
					}
				}
				if ($properties) {
					// AUTHOR
					if (in_array('author', $properties)) {
						$author = $value['author'];
						$dataAuthor = $author ? parent::getProperties('person', ['idperson'=>$author]) : null;
						if (isset($dataAuthor[0])) {
							$data[$key]['author'] = ApiFactory::response()->type('person')->setData($dataAuthor)->ready();
						}
					}
				}
			}
		}
		return parent::sortData($data);
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadfiles
	 * @return array
	 * @throws Exception
	 */
	public function post(array $params = null, array $uploadfiles = null): array
	{
		if(isset($params['isPartOf']) && $params['isPartOf'] === '') {
			unset($params['isPartOf']);
		}
		if ($uploadfiles) {
			return parent::uploadfiles($params, $uploadfiles);
		} else {
			return parent::createWithParent('thing', $params);
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
		return parent::erase('thing',$params);
	}
}
