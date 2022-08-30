<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Maintenance;
use Plinct\Api\Server\Entity;
use ReflectionException;

class Organization extends Entity
{
    /**
     * @var string
     */
    protected string $table = "organization";
    /**
     * @var string
     */
    protected string $type = "Organization";
    /**
     * @var array|string[]
     */
    protected array $properties = ['name','idorganization'];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "address" => "PostalAddress", "contactPoint" => "ContactPoint", "member" => "Person", "location" => "Place", "image" => "ImageObject", "localBusiness" => "LocalBusiness" ];

    /**
     * @param array $params
     * @return array
     */
    public function get(array $params = []): array
    {
        // GET DATA ORGANIZATION
        $data = parent::get($params);

        // GET OFFERS
        if (isset($params['properties']) && strpos($params['properties'], "hasOfferCatalog") !== false) {
            $offerCatalog = (new Offer())->get([ "format" => "ItemList", "offeredBy" => $params['id'], "offeredByType" => "Organization" ]);
            $data[0]['hasOfferCatalog'] = $offerCatalog;
        }

        return $data;
    }

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null) : array
    {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("ContactPoint");
        $message[] = $maintenance->createSqlTable("PostalAddress");
        $message[] = $maintenance->createSqlTable("ImageObject");
        $message[] = $maintenance->createSqlTable("Person");
        $message[] = $maintenance->createSqlTable("Place");
        $message[] = parent::createSqlTable("Organization");
        return $message;
    }
}
