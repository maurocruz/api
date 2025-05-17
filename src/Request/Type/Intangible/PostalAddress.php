<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class PostalAddress extends Entity
{

	public function __construct()
	{
		$this->setTable('postalAddress');
	}

	public function get(array $params = []): array
	{
		$data = parent::get($params);
		return $this->sortData($data);
	}

	public function post(array $params = null): array
	{
		$typeHasPart = $params['typeHasPart'] ?? null;
		$idHaspart = $params['idHasPart'] ?? null;
		$streetAddress = $params['streetAddress'] ?? null;
		$addressLocality = $params['addressLocality'] ?? null;
		$addressRegion = $params['addressRegion'] ?? null;
		$addressCountry = $params['addressCountry'] ?? null;
		$postalCode = $params['postalCode'] ?? null;
		// verifica se existe termos obrigatórios
		if ($streetAddress || $addressLocality || $addressRegion || $addressCountry || $postalCode) {
			$dataPost = parent::post($params);
			if (isset($dataPost['status']) && $dataPost['status'] == 'success' && $typeHasPart && $idHaspart) {
				$idpostalAddress = $dataPost['data'][0]['idpostalAddress'];
				$dataHasPartPut = ApiFactory::request()->type($typeHasPart)->put(["id$typeHasPart" => $idHaspart, "address"=>$idpostalAddress])->ready();
				if (isset($dataHasPartPut['status']) && $dataHasPartPut['status'] == 'success') {
					$dataHasPartPut['data'][] = $dataPost;
					return $dataHasPartPut;
				} else {
					return $dataHasPartPut;
				}
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
