<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\GetData\GetData;

class Review extends CreativeWork
{
	public function __construct()
	{
		parent::__construct();
		$this->setTable('review');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('review');
		$getData->setParams($params);
		$data = $getData->render();

		if (isset($data['error'])) {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		}

		if ($properties) {
			foreach ($data as $key => $value) {
				$itemReviewed = $value['itemReviewed'];
				if (in_array('itemReviewed', $properties)) {
					$dataItemReviewed = ApiFactory::request()->type('thing')->get(['idthing' => $itemReviewed, 'hasPart'=>true])->ready();
					$data[$key]['itemReviewed'] = isset($dataItemReviewed[0]) ? ApiFactory::response()->type($dataItemReviewed[0]['type'])->setData($dataItemReviewed[0])->ready() : null;
				}
			}
		}
		return $this->sortData($data);
	}

	/**
	 * @param string $table
	 * @param array $params
	 * @return array
	 */
	public static function getTypeOrderByReview(string $table, array $params = []): array
	{
		$reviewAspect = $params['reviewAspect'] ?? null;
		// SQL QUERY
		$sql = "SELECT thing.*, $table.*, AVG(reviewRating) AS ratingValue, COUNT(reviewRating) AS reviewCount FROM $table "
			."JOIN thing ON thing.idthing=$table.thing "
			."LEFT JOIN review ON review.itemReviewed=thing.idthing "
			."AND reviewAspect='".$reviewAspect."' "
			."GROUP BY idthing ORDER BY ratingValue DESC;";
		$dataPlace = PDOConnect::run($sql);
		// LOOP
		foreach ($dataPlace as $key => $place) {
			$dataPlace[$key]['publicAccess'] = (bool)$place['publicAccess'];
			$dataPlace[$key]['AggregateRating'] = [
				"@type" => "AggregateRating",
				"ratingValue" => $place['ratingValue'],
				"reviewCount" => $place['reviewCount']
			];
			unset($dataPlace[$key]['ratingValue']);
			unset($dataPlace[$key]['reviewCount']);
		}
		return $dataPlace;
	}

	public function post(array $params = null, array $uploadfiles = null): array
	{
		$itemReviewed = $params['itemReviewed'] ?? null;
		$reviewAspect = $params['reviewAspect'] ?? null;
		$author = $params['author'] ?? null;
		$reviewRating = $params['reviewRating'] ?? null;
		$params['name'] = "Review: ".$reviewAspect;
		if (!$itemReviewed || !$reviewAspect || !$author || !$reviewRating) {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: itemReviewed, reviewAspect, author and reviewRating']);
		} else {
			$params['type'] = 'Review';
			return parent::createWithParent('creativeWork', $params);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::update('creativeWork', $params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('creativeWork', $params);
	}
}