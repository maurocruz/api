<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Taxon;

use Plinct\Api\Request\Server\Entity;

class Taxon extends Entity
{
	public function __construct()
	{
		parent::setTable('taxon');
	}

	public function get(array $params = []): array
	{
		$name = $params['name'] ?? null;
		$orderBy = $params['orderBy'] ?? null;
		$nameLike = $params['nameLike'] ?? null;
		$vernacularNameLike = $params['vernacularNameLike'] ?? null;
		$properties = $params['properties'] ?? null;

		if ($nameLike || $vernacularNameLike) {
			$dataThing = parent::getThingFirst('Taxon', ['nameLike'=>$nameLike,'orderBy'=>'name']);
			$dataTaxon = parent::getData(['vernacularNameLike'=>$vernacularNameLike,'orderBy'=>'vernacularName'], true);
			$returns = array_merge($dataThing, $dataTaxon);
			$newData = [];
			array_walk($returns, function ($item, $key) use (&$newData) {
				$newData[$item['thing']] = $item;
			});
			$returns = $newData;
		}
		elseif ($name || $orderBy) {
			$returns = parent::getThingFirst('Taxon', $params);
		}
		else {
			$returns = parent::getData($params, true);
		}

		// PROPERTIES
		if ($properties) {
			foreach ($returns as $key => $value) {
				$idtaxon = $value['idtaxon'];
				$idthing = $value['thing'];
				if (stripos($properties,'childTaxon') !== false) $value['childTaxon'] = parent::getProperties('taxon',['parentTaxon' => $idtaxon]);
				if (stripos($properties,'image') !== false) $value['image'] = parent::getProperties('imageObject', ['isPartOf' => $idthing, 'orderBy' => 'position']);
				$returns[$key] = $value;
			}
		}
		return $this->sortData($returns);
	}
}
