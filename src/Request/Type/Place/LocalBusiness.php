<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Place;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;

class LocalBusiness extends Entity
{
  public function __construct()
  {
		$this->setTable("localBusiness");
  }

	public function get(array $params = []): array
	{
		$additionalTypeLike = $params['additionalTypeLike'] ?? null;
		$orderBy = $params['orderBy'] ?? null;
		$ordering = $params['ordering'] ?? null;
		$nameLike = $params['nameLike'] ?? null;
		$idlocalBusiness = $params['idlocalBusiness'] ?? null;
		$idthing = $params['idthing'] ?? null;

		$sql = "SELECT *, AVG(reviewRating) AS ratingValue, COUNT(reviewRating) AS reviewCount FROM `localBusiness` 
  INNER JOIN `thing` ON `thing`.idthing=`localBusiness`.thing";
  	if ($nameLike) {
			$sql .= " AND `thing`.`name` LIKE '%$nameLike%'";
		}
		$sql .= " LEFT JOIN `place` ON `place`.idplace=`localBusiness`.location 
LEFT JOIN `geoCoordinates` on `geoCoordinates`.idgeoCoordinates = `place`.geo 
LEFT JOIN `postalAddress` ON `postalAddress`.idpostalAddress = `geoCoordinates`.`address` 
LEFT JOIN `organization` ON `organization`.idorganization=`localBusiness`.organization
LEFT JOIN `review` ON `review`.itemReviewed=`thing`.idthing";
		if ($idlocalBusiness) {
			$sql .= " WHERE `place`.idplace = '$idlocalBusiness'";
		} else if ($idthing) {
			$sql .= " WHERE `thing`.idthing = '$idthing'";
		} elseif ($additionalTypeLike !== null) {
			$sql .= " WHERE `additionalType` LIKE '%$additionalTypeLike%'";
		}
		$sql .= " GROUP BY `localBusiness`.idlocalBusiness";
		if ($orderBy !== null) {
			$sql .= " ORDER BY $orderBy $ordering";
		}
		$sql .= ";";
		$data = PDOConnect::run($sql);
		foreach ($data as $key => $localBusiness) {
			if ($localBusiness['idlocalBusiness'] == null) return [];
			$address = $localBusiness['idpostalAddress']
				? ApiFactory::response()->type('PostalAddress')->setData([[
					'idpostalAddress' => $localBusiness['idpostalAddress'],
					'addressCountry' => $localBusiness['addressCountry'],
					"addressLocality" => $localBusiness['addressLocality'],
					"addressRegion" => $localBusiness['addressRegion'],
					"streetAddress" => $localBusiness['streetAddress'],
					"postalCode" => $localBusiness['postalCode']
				]])->ready()
				: null;
			$geo = $localBusiness['idgeoCoordinates']
				? ApiFactory::response()->type('GeoCoordinates')->setData([[
					'address' => $address[0] ?? null,
					'elevation' => $localBusiness['elevation'],
					'latitude' => $localBusiness['latitude'],
					'longitude' => $localBusiness['longitude'],
					'idgeoCoordinates' => $localBusiness['idgeoCoordinates']
				]])->ready()
				: null;

			$data[$key]['geo'] = $geo[0] ?? null;

			unset($data[$key]['address']);
			unset($data[$key]['idpostalAddress']);
			unset($data[$key]['idgeoCoordinates']);
			unset($data[$key]['idreview']);
			unset($data[$key]['addressCountry']);
			unset($data[$key]['addressLocality']);
			unset($data[$key]['addressRegion']);
			unset($data[$key]['streetAddress']);
			unset($data[$key]['postalCode']);
			unset($data[$key]['elevation']);
			unset($data[$key]['latitude']);
			unset($data[$key]['longitude']);
			unset($data[$key]['location']);
			unset($data[$key]['organization']);
			// aggregateRating
			$data[$key]['aggregateRating'] = $localBusiness['ratingValue']
				? [
					"@type" => "AggregateRating",
					"ratingValue" => $localBusiness['ratingValue'],
					"reviewCount" => $localBusiness['reviewCount'],
				]
				: null;
			unset($data[$key]['ratingValue']);
			unset($data[$key]['reviewCount']);
			// publicAccess
			$data[$key]['publicAccess'] = (bool) $localBusiness['publicAccess'];
		}
		return parent::sortData($data);
	}

	/**
  * @param array|null $params
  * @return array
  */
  public function post(?array $params = null): array
  {
    $params['dateCreated'] = date("Y-m-d");
    return parent::post($params);
  }
}
