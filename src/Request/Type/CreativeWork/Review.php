<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;

class Review extends Entity
{
	public function __construct()
	{
		$this->setTable('review');
	}

	public function get(array $params = []): array
	{
		$itemReviewed = $params['itemReviewed'] ?? null;
		$orderBy = $params['orderBy'] ?? null;
		$ordering = $params['ordering'] ?? null;
		$idreview = $params['idreview'] ?? null;
		//
		$sqlQuery = "SELECT * FROM `review` "
			."LEFT JOIN `thing` ON `thing`.idthing = `review`.thing "
			."LEFT JOIN `creativeWork` ON `creativeWork`.idcreativeWork = `review`.creativeWork";
		if ($idreview) {
			$sqlQuery .= " WHERE `review`.idreview = '$idreview'";
		} elseif ($itemReviewed) {
			$sqlQuery .= " WHERE `review`.itemReviewed = '$itemReviewed'";
		}
		if ($orderBy) {
			$sqlQuery .= " ORDER BY `{$orderBy}` $ordering";
		}
		$sqlQuery .= ";";
		$data = PDOConnect::run($sqlQuery);

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

	public function post(array $params = null): array
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