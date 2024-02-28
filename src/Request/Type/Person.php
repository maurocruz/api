<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;

class Person extends Entity
{
  /**
   * @var string
   */
  protected string $table = "person";
  /**
   * @var string
   */
  protected string $type = "Person";
  /**
   * @var array|string[]
   */
  protected array $properties = ["thing"];
  /**
   * @var array|string[]
   */
  protected array $hasTypes = ['thing'=>'Thing',"address" => 'PostalAddress', "contactPoint" => "ContactPoint", "image" => "ImageObject"];

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = $params['properties'] ?? null;
		$newData = [];
		$getData = new GetData('person');
		$getData->setParams($params)->render();
		$data = $getData->render();
		if (!empty($data)) {
			foreach ($data as $value) {
				$idthing = $value['thing'];
				$dataThing = ApiFactory::request()->type('thing')->get(['idthing' => $idthing])->ready();
				$value['thing'] = $dataThing[0];
				if ($properties) {
					if (stripos($properties, 'contactPoint') !== false) {
						$dataContactPoint = ApiFactory::request()->type('contactPoint')->get(['thing' => $idthing])->ready();
						$value['contactPoint'] = isset($dataContactPoint[0]) ? $dataContactPoint : null;
					}
				}
				$newData[] = $value;
			}
		}
		return $newData;
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadedFiles
	 * @return string[]
	 */
  public function post(array $params = null, array $uploadedFiles = null): array
  {
		$params['type'] = 'person';
		$dataThing = ApiFactory::request()->type('thing')->post($params, $uploadedFiles)->ready();
		$idthing = $dataThing['id'];
		return parent::post(['thing'=>$idthing]);
  }

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		$idthing = $params['thing'] ?? null;
		$idperson = $params['idperson'];
		if (!$idthing) {
			$dataPerson = ApiFactory::request()->type('person')->get(['idperson'=>$idperson])->ready();
			if (isset($dataPerson[0])) {
				$value = $dataPerson[0];
				$idthing = $value['thing']['idthing'];
			} else {
				return $dataPerson;
			}
		}
		ApiFactory::request()->type('thing')->put(['idthing' => $idthing, 'dateModified' => date('Y-m-d H:i:s')])->ready();
		return parent::put($params);
	}
}
