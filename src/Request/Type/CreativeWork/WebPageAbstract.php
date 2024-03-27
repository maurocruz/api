<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

abstract class WebPageAbstract extends Entity
{
	/**
	 * @param string $properties
	 * @param array $dataCreativeWork
	 * @param array $item
	 * @return array
	 */
	protected function getProperties(string $properties, array $valueCreativeWork, array $item): array
	{
		$idcreativeWork = $valueCreativeWork['idcreativeWork'];
		if (stripos($properties, 'hasPart') !== false) {
			$dataWebPageElement = ApiFactory::request()->type('webPageElement')->get(['isPartOf' => $idcreativeWork])->ready();
			$item['hasPart'] = ApiFactory::response()->type('webPageElement')->setData($dataWebPageElement)->ready();
		}
		if (stripos($properties,'imageObject') !== false || stripos($properties,'image') !== false) {
			$thing = $valueCreativeWork['thing'];
			$dataImageObject = ApiFactory::request()->type('imageObject')->get(['isPartOf'=>$thing])->ready();
			$item['image'] = isset($dataImageObject[0]) ? ApiFactory::response()->type('imageObject')->setData($dataImageObject)->ready() : null;
		}
		if (stripos($properties,'isPartOf') !== false) {
			$isPartOf = $valueCreativeWork['isPartOf'];
			$dataWebSite = ApiFactory::request()->type('webSite')->get(['creativeWork'=>$isPartOf])->ready();
			$item['isPartOf'] = ApiFactory::response()->type('webSite')->setData($dataWebSite)->ready()[0];
		}
		return $item;
	}
}