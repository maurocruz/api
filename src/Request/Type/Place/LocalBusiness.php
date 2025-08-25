<?php
namespace Plinct\Api\Request\Type\Place;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\Relationship;

class LocalBusiness extends Place
{
	/**
	 *
	 */
  public function __construct()
  {
		$this->setTable("localBusiness");
  }

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('localBusiness');
		$getData->setLeftJoin('organization', '`organization`.idorganization=`localBusiness`.organization');
		if ($properties) {
			if (in_array('address', $properties)) {
				$getData->setLeftJoin('postalAddress', '`postalAddress`.idpostalAddress = `organization`.address');
			}
			// LOCATION
			if (in_array('location', $properties)) {
				$getData->setLeftJoin('place', '`place`.idplace=`localBusiness`.location');
				$getData->setLeftJoin('geoCoordinates', '`geoCoordinates`.idgeoCoordinates = `place`.geo');
				$getData->setLeftJoin('postalAddress', '`postalAddress`.idpostalAddress = `geoCoordinates`.address', 'pg');
			}
			// review
			if (in_array('review', $properties)) {
				$getData->setFields('*, AVG(reviewRating) AS ratingValue, COUNT(reviewRating) AS reviewCount');
				$getData->setLeftJoin('review', '`review`.itemReviewed=`thing`.idthing');
			}
		}
		$getData->setParams($params);
		$data = $getData->render();
		foreach ($data as $key => $item) {
			$idthing = $item['idthing'];
			// ADDRESS
			$address = isset($item['idpostalAddress']) ? ApiFactory::response()->type('PostalAddress')->setData([[
				'idpostalAddress' => $item['idpostalAddress'],
				'addressCountry' => $item['addressCountry'],
				"addressLocality" => $item['addressLocality'],
				"addressRegion" => $item['addressRegion'],
				"streetAddress" => $item['streetAddress'],
				"postalCode" => $item['postalCode']
			]])->ready() : null;
			$data[$key]['address'] = $address[0] ?? $item['address'] ?? null;
			// PROPERTIES
			if ($properties) {
				// CONTACT POINT
				if (in_array('contactPoint', $properties)) {
					$dataContactPoint = ApiFactory::request()->type('contactPoint')->get(['typeHasPart'=>'LocalBusiness','idHasPart'=>$idthing])->ready();
					if ($dataContactPoint) {
						$data[$key]['contactPoint'] = ApiFactory::response()->type('contactPoint')->setData($dataContactPoint)->ready();
					}
				}
				// LOCATION
				if (in_array('location', $properties)) {
					$geo = $item['idgeoCoordinates'] ? ApiFactory::response()->type('GeoCoordinates')->setData([[
						'address' => $address[0] ?? null,
						'elevation' => $item['elevation'],
						'latitude' => $item['latitude'],
						'longitude' => $item['longitude'],
						'idgeoCoordinates' => $item['idgeoCoordinates']
					]])->ready() : null;
					$data[$key]['geo'] = $geo[0] ?? null;
					unset($data[$key]['elevation']);
					unset($data[$key]['latitude']);
					unset($data[$key]['longitude']);
					unset($data[$key]['location']);
					// publicAccess
					$data[$key]['publicAccess'] = (bool)$item['publicAccess'];
				}
				// REVIEW
				if (in_array('review', $properties)) {
					// aggregateRating
					$data[$key]['aggregateRating'] = $item['ratingValue'] ? ["@type" => "AggregateRating", "ratingValue" => $item['ratingValue'], "reviewCount" => $item['reviewCount']] : null;
					unset($data[$key]['idreview']);
					unset($data[$key]['ratingValue']);
					unset($data[$key]['reviewCount']);
				}
				// HAS PART
				if (in_array('hasPart', $properties)) {
					$hasPart = new Relationship();
					$hasPart->setTypeHasPart('LocalBusiness');
					$hasPart->setIdHasPart($idthing);
					if (isset($params['typeIsPartOf'])) {
						$hasPart->setTypeIsPartOf($params['typeIsPartOf']);
					}
					$dataHasPart = $hasPart->get();
					if ($dataHasPart) {
						foreach ($dataHasPart as $valueHasPart) {
							$typeHasPart = $valueHasPart['@type'];
							if (strtolower($typeHasPart) == strtolower('ImageObject')) {
								$data[$key]['subjectOf'][] = ApiFactory::response()->type('imageObject')->setData($valueHasPart)->ready();
							}
							if ($typeHasPart == 'ContactPoint') {
								$data[$key]['contactPoint'][] = ApiFactory::response()->type('contactPoint')->setData($valueHasPart)->ready();
							}
						}
					}
				}
			}
			unset($data[$key]['organization']);
			unset($data[$key]['idpostalAddress']);
			unset($data[$key]['idgeoCoordinates']);
			unset($data[$key]['addressCountry']);
			unset($data[$key]['addressLocality']);
			unset($data[$key]['addressRegion']);
			unset($data[$key]['streetAddress']);
			unset($data[$key]['postalCode']);
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
