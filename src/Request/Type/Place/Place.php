<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Place;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;

class Place extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('place');
	}

	public function get(array $params = []): array
	{
		$orderBy = $params['orderBy'] ?? null;
		$ordering = $params['ordering'] ?? null;
		$limit = $params['limit'] ?? null;
		$offset = $params['offset'] ?? null;
		$idplace = $params['idplace'] ?? $params['id'] ?? null;
		$idthing = $params['idthing'] ?? $params['thing'] ?? null;
		$nameLike = $params['nameLike'] ?? null;
		$reviewAspect = $params['reviewAspect'] ?? null;

		$sqlQuery = "SELECT *, AVG(reviewRating) as ratingValue, COUNT(reviewRating) as reviewCount FROM `place` 
  INNER JOIN `thing` ON `thing`.idthing = `place`.`thing`";
		if ($nameLike) {
			$sqlQuery .= " AND `thing`.`name` LIKE '%$nameLike%'";
		}
		$sqlQuery .= " LEFT JOIN `geoCoordinates` on `geoCoordinates`.idgeoCoordinates = `place`.geo 
  LEFT JOIN `postalAddress` ON `postalAddress`.idpostalAddress = `geoCoordinates`.`address` 
  LEFT JOIN `review` ON `review`.itemReviewed = `thing`.idthing";
		if ($reviewAspect) {
			$sqlQuery .= " AND `review`.reviewAspect = '$reviewAspect'";
		}
		if ($idplace) {
			$sqlQuery .= " WHERE `place`.idplace = '$idplace'";
		} else if ($idthing) {
			$sqlQuery .= " WHERE `thing`.idthing = '$idthing'";
		} else {
			$sqlQuery .= " GROUP BY idthing";
		}
		if ($orderBy) {	$sqlQuery .= ' ORDER BY ' . $orderBy . " ".$ordering; }
		if ($limit) {	$sqlQuery .= ' LIMIT ' . $limit;	}
		if ($offset) {	$sqlQuery .= ' OFFSET ' . $offset;	}
		$sqlQuery .= ";";
		$data = PDOConnect::run($sqlQuery);

		foreach ($data as $key => $place) {
			if ($place['idplace'] == null) return [];

			$address = $place['idpostalAddress']
				? ApiFactory::response()->type('PostalAddress')->setData([[
					'idpostalAddress' => $place['idpostalAddress'],
					'addressCountry' => $place['addressCountry'],
					"addressLocality" => $place['addressLocality'],
					"addressRegion" => $place['addressRegion'],
					"streetAddress" => $place['streetAddress'],
					"postalCode" => $place['postalCode']
				]])->ready()
				: null;
			$geo = $place['idgeoCoordinates']
				? ApiFactory::response()->type('GeoCoordinates')->setData([[
					'address' => $address[0] ?? null,
					'elevation' => $place['elevation'],
					'latitude' => $place['latitude'],
					'longitude' => $place['longitude'],
					'idgeoCoordinates' => $place['idgeoCoordinates']
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
			// aggregateRating
			$data[$key]['aggregateRating'] = $place['ratingValue']
				? [
				"@type" => "AggregateRating",
				"ratingValue" => $place['ratingValue'],
				"reviewCount" => $place['reviewCount'],
				]
				: null;
			unset($data[$key]['ratingValue']);
			unset($data[$key]['reviewCount']);
			// publicAccess
			$data[$key]['publicAccess'] = (bool) $place['publicAccess'];
		}
		return parent::sortData($data);
	}

	/**
	 * @param array|null $params
	 * @return string[]
	 */
	public function post(array $params = null): array
	{
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
