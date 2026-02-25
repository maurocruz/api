<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\Relationship;

class GeoCoordinates extends Entity
{
	/**
	 *
	 */
	public function __construct(Relationship $relationship = null)
	{
		parent::__construct($relationship);
		$this->setTable('geoCoordinates');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$tableHasPart = $params['tableHasPart'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		$getData = new GetData('geoCoordinates');
		if ($tableHasPart == 'place' && $idHasPart !== null) {
			$getData->setLeftJoin($tableHasPart,"`$tableHasPart`.geo = `geoCoordinates`.idgeoCoordinates");
		}
		if (in_array('address', $properties)) {
			$getData->setLeftJoin('postalAddress',"`postalAddress`.idpostalAddress = `geoCoordinates`.address");
		}
		$getData->setParams($params);
		$data = $getData->render();

		foreach ($data as $key => $item) {
			$item['type'] = "GeoCoordinates";
			$item['address'] = isset($item['idpostalAddress'])
				? ApiFactory::response()->type('postalAddress')->setData([
					"idpostalAddress" => $item['idpostalAddress'],
					"addressCountry" => $item['addressCountry'],
					"addressLocality" => $item['addressLocality'],
					"addressRegion" => $item['addressRegion'],
					"postalCode" => $item['postalCode'],
					"streetAddress" => $item['streetAddress'],
				])->ready() : null;
			unset($item['geo']);
			unset($item['keywords']);
			unset($item['publicAccess']);
			unset($item['addressCountry']);
			unset($item['addressLocality']);
			unset($item['addressRegion']);
			unset($item['postalCode']);
			unset($item['streetAddress']);
			$data[$key] = $item;
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
		$tableHasPart = $params['tableHasPart'] ?? false;
		$idHasPart = $params['idHasPart'] ?? false;
		$latitude = $params['latitude'] ?? null;
		$longitude = $params['longitude'] ?? null;
		$streetAddress = $params['streetAddress'] ?? null;
		$addressLocality = $params['addressLocality'] ?? null;
		$addressRegion = $params['addressRegion'] ?? null;
		$addressCountry = $params['addressCountry'] ?? null;
		$postalCode = $params['postalCode'] ?? null;
		// verifica se existem termos obrigatórios
		if ($tableHasPart !== null && $idHasPart !== null && $latitude !== null && $longitude !== null) {
			// verifica se existe item has part
			$dataTableHasPart = ApiFactory::request()->type($tableHasPart)->get(["id$tableHasPart"=>$idHasPart])->ready();
			if(empty($dataTableHasPart)) {
				return ApiFactory::response()->message()->fail()->generic(["TableHasPart item not found (id$tableHasPart in $tableHasPart not exists)"]);
			} else {
				// start transacion
				PDOConnect::run("SET autocommit = 0; START TRANSACTION;");
				// cria geoCoodinates
				$postGeoCoordinates = parent::post($params);
				if (isset($postGeoCoordinates['status']) && $postGeoCoordinates['status'] === 'success') {
					$data = $postGeoCoordinates['data'];
					if (!empty($data)) {
						$idgeoCoordinates = $data[0]['idgeoCoordinates'];
						$postGeoCoordinates['data'] = $data[0];
						// adiciona idgeoCoordinates na tabela has part
						$putTableHasPart = ApiFactory::request()->type($tableHasPart)->put(["id$tableHasPart" => $idHasPart, "geo" => $idgeoCoordinates])->ready();
						// verifica se foi adicionado idgeoCoordinates na tabela has part
						if (isset($putTableHasPart['status']) && $putTableHasPart['status'] == 'success') {
							PDOConnect::run("COMMIT; SET autocommit = 1;");
							// verifica se existem dados de PostalAddress
							if ($streetAddress && $addressCountry && $addressLocality && $addressRegion && $postalCode) {
								// salva postalAddress
								$postPostalAddress = ApiFactory::request()->type('postalAddress')->post([
									"streetAddress" => $streetAddress,
									"addressLocality" => $addressLocality,
									"addressRegion" => $addressRegion,
									"addressCountry" => $addressCountry,
									"postalCode" => $postalCode,
									"tableHasPart" => "geoCoordinates",
									"idHasPart" => $idgeoCoordinates
								])->ready();
								if (isset($postPostalAddress['status']) && $postPostalAddress['status'] == 'success') {
									$idpostalAddress = $postPostalAddress['data'][0]['idpostalAddress'];
									$postGeoCoordinates['data']['address'] = $idpostalAddress;
									$postGeoCoordinates['data'] = $postGeoCoordinates['data'] + $postPostalAddress['data'][0];
								}
							}
							return $postGeoCoordinates;
						} else {
							PDOConnect::run("ROLLBACK; SET autocommit = 1;");
							return ApiFactory::response()->message()->fail()->generic($putTableHasPart);
						}
					} else {
						PDOConnect::run("ROLLBACK; SET autocommit = 1;");
						return ApiFactory::response()->message()->fail()->generic($data);
					}
				} else {
					PDOConnect::run("ROLLBACK; SET autocommit = 1;");
					return ApiFactory::response()->message()->fail()->generic($postGeoCoordinates);
				}
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: tableHasPart, idHasPart, latitude, longitude']);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		$idgeoCoordinates = $params['idgeoCoordinates'] ?? null;
		$streetAddress = $params['streetAddress'] ?? null;
		$addressLocality = $params['addressLocality'] ?? null;
		$addressRegion = $params['addressRegion'] ?? null;
		$addressCountry = $params['addressCountry'] ?? null;
		$postalCode = $params['postalCode'] ?? null;
		if ($idgeoCoordinates !== null) {
			$putGeoCoordinates = parent::put($params);
			if (isset($putGeoCoordinates['status']) && $putGeoCoordinates['status'] === 'success') {
				$dataGeoCoordinates = $putGeoCoordinates['data'][0];
				$putGeoCoordinates['data'] = $dataGeoCoordinates;
				if ($streetAddress || $addressLocality || $addressCountry || $addressRegion || $postalCode) {
					$idpostalAddress = $dataGeoCoordinates['address'];
					$putPostalAddress = ApiFactory::request()->type('postalAddress')->put(['idpostalAddress' => $idpostalAddress] + $params)->ready();
					if (isset($putPostalAddress['status']) && $putPostalAddress['status'] == 'success') {
						$putGeoCoordinates['data'] = $putGeoCoordinates['data'] + $putPostalAddress['data'][0];
					}
				}
				return $putGeoCoordinates;
			} else {
				return ApiFactory::response()->message()->fail()->generic($putGeoCoordinates);
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: idgeoCoordinates']);
		}
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		$idgeoCoordinates = $params['idgeoCoordinates'] ?? null;
		if ($idgeoCoordinates !== null) {
			// delete address
			$dataGeoCoordinates = parent::getData(['idgeoCoordinates'=>$idgeoCoordinates]);
			if (isset($dataGeoCoordinates[0])) {
				$value = $dataGeoCoordinates[0];
				if($value['address']) {
					$idpostalAddress = $value['address'];
					ApiFactory::request()->type('postalAddress')->delete(['idpostalAddress' => $idpostalAddress])->ready();
				}
			}
			return parent::delete($params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: idgeoCoordinates']);
		}
	}
}
