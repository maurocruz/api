<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Person;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Person extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('person');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$returns = [];
		$name = $params['name'] ?? null;
		$url = $params['url'] ?? null;
		if ($name || $url) {
			$dataThing = ApiFactory::request()->type('thing')->get($params)->ready();
			foreach ($dataThing as $item) {
				$idthing = $item['idthing'];
				$dataPerson = parent::getData(['thing' => $idthing] + $params);
				if (isset($dataPerson[0])) {
					$value = $item + $dataPerson[0];
					$idperson = $value['idperson'];
					$value['contactPoint'] = parent::getProperties('contactPoint',['thing' => $idthing]);
					$value['homeLocation'] = parent::getProperties('place',['idplace' => $value['homeLocation'],'properties'=>'address']);
					$value['image'] = parent::getProperties('imageObject',['isPartOf' => $idthing]);
					$value['memberOf'] = parent::getProperties('programMembership',['member' => $idperson]);
					$returns[] = $value;
				}
			}
		} else {
			$data = parent::getData($params);
		}
		if (!empty($data)) {
			foreach ($data as $value) {
				$idthing = $value['thing'];
				$idperson = $value['idperson'];
				$dataThing = ApiFactory::request()->type('thing')->get(['idthing' => $idthing])->ready();
				$value['contactPoint'] = parent::getProperties('contactPoint',['thing' => $idthing]);
				$value['homeLocation'] = parent::getProperties('place',['idplace' => $value['homeLocation'],'properties'=>'address']);
				$value['image'] = parent::getProperties('imageObject',['isPartOf' => $idthing]);
				$value['memberOf'] = parent::getProperties('programMembership',['member' => $idperson]);
				$returns[] = $value + $dataThing[0];
			}
		}

		return parent::array_sort($returns, $params);
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadedFiles
	 * @return string[]
	 */
  public function post(array $params = null, array $uploadedFiles = null): array
  {
	  $params['type'][] = 'Person';
		return parent::createWithParent('thing', $params, $uploadedFiles);
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
