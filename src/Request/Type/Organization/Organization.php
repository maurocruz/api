<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Organization;

use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Type\Offer;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class Organization extends Entity
{
	public function __construct()
	{
		$this->setTable('organization');
	}

	/**
   * @param array $params
   * @return array
   */
  public function get(array $params = []): array
  {
    // GET DATA ORGANIZATION
	  $returns = parent::getData($params);
    // GET OFFERS
    if (isset($params['properties']) && strpos($params['properties'], "hasOfferCatalog") !== false) {
      $offerCatalog = (new Offer())->get([ "format" => "ItemList", "offeredBy" => $params['id'], "offeredByType" => "Organization" ]);
	    $returns[0]['hasOfferCatalog'] = $offerCatalog;
    }
	  return parent::array_sort($returns, $params);
  }

	public function post(array $params = null): array
	{
		$params['type'][] = 'Organization';
		return parent::createWithParent('thing',$params);
	}

	public function put(array $params = null): array
	{
		return parent::update('thing',$params);
	}

	public function delete(array $params): array
	{
		return parent::erase('thing',$params);
	}
}
