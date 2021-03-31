<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class PostalAddress extends Entity implements TypeInterface {
    protected $table = 'postalAddress';
    protected $type = 'PostalAddress';
    protected $properties = [ 'streetAddress', 'addressLocality', 'addressRegion', 'addressCountry', 'postalCode' ];

    public function createSqlTable($type = null): array {
        return parent::createSqlTable("PostalAddress");
    }
}
