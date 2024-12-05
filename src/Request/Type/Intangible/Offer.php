<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;

class Offer extends Entity
{
	/**
	 *
	 */
  public function __construct()
  {
		$this->setTable('offer');
  }

	public function get(array $params = []): array
	{
		$isValidThrough = array_key_exists('isValidThrough', $params);
		$availability = $params['availability'] ?? null;
		$type = $params['type'] ?? null;

		$sqlQuery = "SELECT * FROM offer LEFT JOIN thing ON thing.idthing = offer.itemOffered";
		if ($type == 'service' || $type === null) {
			$sqlQuery .= " LEFT JOIN `service` ON `service`.thing = thing.idthing AND thing.type = 'service'";
		}
		if ($type == 'product' || $type === null) {
			$sqlQuery .= " LEFT JOIN `product` ON `product`.thing = thing.idthing AND thing.type = 'product'";
		}
		if ($isValidThrough || $availability) {
			$sqlQuery .= " WHERE";
		}
		if ($availability) {
			$sqlQuery .= " availability = '$availability'";
		}
		if ($isValidThrough) {
			$sqlQuery .= " AND validThrough >= CURDATE()";
		}
		$sqlQuery .= ";";
		$data = PDOConnect::run($sqlQuery);

		foreach ($data as $key => $value) {
			if (!isset($value['idservice']) && !isset($value['idproduct'])) {
				unset($data[$key]);
			} else {
				$itemOffered = [
					'idthing' => $value['idthing'],
					'additionalType' => $value['additionalType'],
					'alternateName' => $value['alternateName'],
					'dateCreated' => $value['dateCreated'],
					'dateModified' => $value['dateModified'],
					'description' => $value['description'],
					'disambiguatingDescription' => $value['disambiguatingDescription'],
					'image' => $value['image'],
					'mainEntityOfPage' => $value['mainEntityOfPage'],
					'name' => $value['name'],
					'type' => $value['type'],
					'url' => $value['url'],
				];
				if (isset($value['idservice'])) {
					$itemOffered['idservice'] = $value['idservice'];
					$itemOffered['thing'] = $value['thing'];
					$itemOffered['provider'] = $value['provider'];
					$itemOffered['category'] = $value['category'];
					$itemOffered['serviceType'] = $value['serviceType'];
					$itemOffered['termsOfService'] = $value['termsOfService'];
				}
				unset($value['idthing']);
				unset($value['additionalType']);
				unset($value['alternateName']);
				unset($value['dateCreated']);
				unset($value['dateModified']);
				unset($value['description']);
				unset($value['disambiguatingDescription']);
				unset($value['image']);
				unset($value['mainEntityOfPage']);
				unset($value['name']);
				unset($value['type']);
				unset($value['url']);
				unset($value['idservice']);
				unset($value['thing']);
				unset($value['provider']);
				unset($value['category']);
				unset($value['serviceType']);
				unset($value['termsOfService']);
				unset($value['idproduct']);
				unset($value['manufacturer']);

				$value['itemOffered'] = ApiFactory::response()->type($itemOffered['type'])->setData($itemOffered)->ready();
				$data[$key] = $value;
			}
		}
		return parent::sortData($data);
	}
}
