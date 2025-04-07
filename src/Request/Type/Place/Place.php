<?php
namespace Plinct\Api\Request\Type\Place;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;

class Place extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('place');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$orderBy = $params['orderBy'] ?? null;
		$fields = $params['fields'] ?? null;
		$getData = new GetData('place');
		if (in_array('geo',$properties)) {
			$getData->setLeftJoin('geoCoordinates','`geoCoordinates`.idgeoCoordinates = `place`.geo');
			$getData->setLeftJoin('postalAddress','`postalAddress`.idpostalAddress = `geoCoordinates`.address');
		}
		if ($orderBy=='reviewRating' || in_array('aggregateRating',$properties)) {
			$params['groupBy'] = 'idplace';
			$getData->setFields('*,AVG(reviewRating) as ratingValue, COUNT(reviewRating) as reviewCount');
			$getData->setLeftJoin('review','`review`.itemReviewed = `thing`.idthing');
		}
		$getData->setParams($params);
		$data = $getData->render();

		if ($fields && str_contains($fields,'count') && isset($data[0])) {
			return $data[0];
		}

		foreach ($data as $key => $place) {
			if (!isset($place['idplace']) || $place['idplace'] == null) return [];
			$idthing = $place['idthing'];
			// REVIEW
			if (in_array('review', $properties)) {
				$dataReview = ApiFactory::request()->type('review')->get(['itemReviewed'=>$idthing])->ready();
				if(isset($dataReview[0])) {
					$data[$key]['review'] = ApiFactory::response()->type('review')->setData($dataReview)->ready();
				}
			}
			// GEO
			if (in_array('geo',$properties)) {
				$address = isset($place['idpostalAddress'])
					? ApiFactory::response()->type('PostalAddress')->setData([[
						'idpostalAddress' => $place['idpostalAddress'],
						'addressCountry' => $place['addressCountry'],
						"addressLocality" => $place['addressLocality'],
						"addressRegion" => $place['addressRegion'],
						"streetAddress" => $place['streetAddress'],
						"postalCode" => $place['postalCode']
					]])->ready()
					: null;
				$geo = isset($place['idgeoCoordinates'])
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
				unset($data[$key]['addressCountry']);
				unset($data[$key]['addressLocality']);
				unset($data[$key]['addressRegion']);
				unset($data[$key]['streetAddress']);
				unset($data[$key]['postalCode']);
				unset($data[$key]['elevation']);
				unset($data[$key]['latitude']);
				unset($data[$key]['longitude']);
			}
			// AGGREGATE RATING
			if (in_array('aggregateRating',$properties)) {
				$data[$key]['aggregateRating'] = $place['ratingValue']
					? [
						"@type" => "AggregateRating",
						"ratingValue" => $place['ratingValue'],
						"reviewCount" => $place['reviewCount'],
					]
					: null;
				unset($data[$key]['ratingValue']);
				unset($data[$key]['reviewCount']);
			}
			// REVIEW
			if ($orderBy=='reviewRating' || in_array('review',$properties)) {
				unset($data[$key]['itemReviewed']);
				unset($data[$key]['reviewAspect']);
				unset($data[$key]['reviewBody']);
				unset($data[$key]['reviewRating']);
			}
			// publicAccess
			$data[$key]['publicAccess'] = (bool) $place['publicAccess'];
		}
		return parent::sortData($data);
	}

	/**
	 * @param array $data
	 * @param string $key
	 * @param array $value
	 * @return array
	 */
	public static function wrapperLocation(array $data, string $key, array $value): array
	{
		$address = isset($value['idpostalAddress'])
			? ApiFactory::response()->type('PostalAddress')->setData([
				'idpostalAddress' => $value['idpostalAddress'],
				'addressCountry' => $value['addressCountry'],
				"addressLocality" => $value['addressLocality'],
				"addressRegion" => $value['addressRegion'],
				"streetAddress" => $value['streetAddress'],
				"postalCode" => $value['postalCode']
			])->ready()
			: null;
		$geo = isset($value['idgeoCoordinates'])
			? ApiFactory::response()->type('GeoCoordinates')->setData([
				'address' => $address ?? null,
				'elevation' => $value['elevation'],
				'latitude' => $value['latitude'],
				'longitude' => $value['longitude'],
				'idgeoCoordinates' => $value['idgeoCoordinates']
			])->ready()
			: null;
		$place = isset($value['idplace'])
			? ApiFactory::response()->type('Place')->setData([
				'idplace'=>$value['idplace'],
				'geo'=>$geo ?? null,
				'publicAccess' => (bool) $value['publicAccess'],
			])->ready() : null;
		$data[$key]['location'] = $place ?? null;
		unset($data[$key]['idplace']);
		unset($data[$key]['geo']);
		unset($data[$key]['publicAccess']);
		unset($data[$key]['address']);
		unset($data[$key]['idpostalAddress']);
		unset($data[$key]['idgeoCoordinates']);
		unset($data[$key]['addressCountry']);
		unset($data[$key]['addressLocality']);
		unset($data[$key]['addressRegion']);
		unset($data[$key]['streetAddress']);
		unset($data[$key]['postalCode']);
		unset($data[$key]['elevation']);
		unset($data[$key]['latitude']);
		unset($data[$key]['longitude']);
		return $data;
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
