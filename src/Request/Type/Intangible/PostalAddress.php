<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;

class PostalAddress extends Entity
{

	public function __construct()
	{
		$this->setTable('postalAddress');
	}

	public function get(array $params = []): array
	{
		$tableHasPart = $params['tableHasPart'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		if ($tableHasPart !== null && $idHasPart !== null) {
			$sqlQuery = "SELECT * FROM `postalAddress` LEFT JOIN `$tableHasPart` ON `$tableHasPart`.address = `postalAddress`.idpostalAddress";
			$sqlQuery .= ";";
			$data = PDOConnect::run($sqlQuery);
		} else {
			$data = parent::getData($params);
		}
		return $this->sortData($data);
	}

	public function post(array $params = null): array
	{
		$tableHasPart = $params['tableHasPart'] ?? false;
		$idHasPart = $params['idHasPart'] ?? false;
		$streetAddress = $params['streetAddress'] ?? null;
		$addressLocality = $params['addressLocality'] ?? null;
		$addressRegion = $params['addressRegion'] ?? null;
		$addressCountry = $params['addressCountry'] ?? null;
		$postalCode = $params['postalCode'] ?? null;

		// verifica se existe termos obrigatórios
		if ($streetAddress && $addressLocality && $addressRegion && $addressCountry && $postalCode && $tableHasPart && $idHasPart) {

			// verifica se existe item has part
			$dataTableHasPart = ApiFactory::request()->type($tableHasPart)->get(["id$tableHasPart"=>$idHasPart])->ready();
			if (empty($dataTableHasPart)) {
				return ApiFactory::response()->message()->fail()->generic(["TableHasPart item not found (id$tableHasPart in $tableHasPart not exists)"]);
			} else {

				// cria postalAddress
				$returnsPost = parent::post($params);
				if (isset($returnsPost['status']) && $returnsPost['status'] === 'success') {
					$data = $returnsPost['data'];
					if (!empty($data)) {
						$idpostalAddress = $data[0]['idpostalAddress'];
						$putTableHasPart = ApiFactory::request()->type($tableHasPart)->put(["id$tableHasPart" => $idHasPart, 'address'=>$idpostalAddress])->ready();

						// verifica se foi adicionado idgeoCoordinates na tabela has part
						if (isset($putTableHasPart['status']) && $putTableHasPart['status'] == 'success') {
							return $returnsPost;
						} else {
							return ApiFactory::response()->message()->fail()->generic(["TableHasPart item not found"]);
						}
					} else {
						return ApiFactory::response()->message()->fail()->generic($data);
					}
				} else {
					return ApiFactory::response()->message()->fail()->generic($returnsPost);
				}
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: streetAddress, addressLocality, addressRegion, addressCountry, postalCode, tableHasPart, idHasPart']);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::put($params);
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
