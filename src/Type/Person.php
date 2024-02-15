<?php
declare(strict_types=1);
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class Person extends Entity
{
    /**
     * @var string
     */
    protected string $table = "person";
    /**
     * @var string
     */
    protected string $type = "Person";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "*" ];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "address" => 'PostalAddress', "contactPoint" => "ContactPoint", "image" => "ImageObject" ];

    /**
     * @param array $params
     * @return string[]
     */
    public function post(array $params): array
    {
        if (isset($params['tableHasPart']) && isset($params['idHasPart']) ) {
            return parent::post($params);
        } elseif (isset($params['givenName']) && isset($params['familyName'])) {
            $params['name'] = $params['givenName']." ".$params['familyName'];
            return parent::post($params);
        } else {
            return [ "message" => "incomplete mandatory data" ];
        } 
    }

    /**
     * @param ?array $params
     * @return array
     */
    public function put(?array $params = []): array
    {
        $params['name'] = $params['givenName']." ".$params['familyName'];
        return parent::put($params);
    }

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null) : array
    {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("PostalAddress");        
        $message[] = $maintenance->createSqlTable("ContactPoint");         
        $message[] = $maintenance->createSqlTable("ImageObject");
        $message[] = parent::createSqlTable("Person");
        return $message;
    }
}
