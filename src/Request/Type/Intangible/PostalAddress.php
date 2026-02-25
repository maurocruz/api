<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\Relationship;

class PostalAddress extends Entity
{
	/**
	 *
	 */
	public function __construct(Relationship $relationship = null)
	{
		parent::__construct($relationship);
		$this->setTable('postalAddress');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$data = parent::get($params);
		return $this->sortData($data);
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadfiles
	 * @return array
	 */
	public function post(array $params = null, array $uploadfiles = null): array
	{
		$typeHasPart = $params['typeHasPart'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		$streetAddress = $params['streetAddress'] ?? null;
		$addressLocality = $params['addressLocality'] ?? null;
		$addressRegion = $params['addressRegion'] ?? null;
		$addressCountry = $params['addressCountry'] ?? null;
		$postalCode = $params['postalCode'] ?? null;
		// verifica se existe termos obrigatórios
		if ($streetAddress || $addressLocality || $addressRegion || $addressCountry || $postalCode) {
			$dataPost = parent::post($params);
			if (isset($dataPost['status']) && $dataPost['status'] == 'success' && $typeHasPart && $idHasPart) {
				$idpostalAddress = $dataPost['data'][0]['idpostalAddress'];
				$dataHasPartPut = ApiFactory::request()->type($typeHasPart)->put(["id$typeHasPart"=>$idHasPart,"address"=>$idpostalAddress])->ready();
				if (isset($dataHasPartPut['status']) && $dataHasPartPut['status'] == 'success') {
					$dataHasPartPut['data'][] = $dataPost + $dataHasPartPut;
				}
				return $dataHasPartPut;
			} else {
				return $dataPost;
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: streetAddress or addressLocality or addressRegion or addressCountry or postalCode']);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		$idpostalAddress = $params['idpostalAddress'] ?? null;
		if ($idpostalAddress) {
			return parent::put($params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['idpostalAddress']);
		}

	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		$idpostalAddress = $params['idpostalAddress'] ?? null;
		if ($idpostalAddress !== null) {
			return parent::delete($params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: idpostalAddress']);
		}
	}
}
