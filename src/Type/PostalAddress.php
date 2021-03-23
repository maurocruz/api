<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class PostalAddress extends Entity implements TypeInterface {
    protected string $table = 'postalAddress';
    protected string $type = 'PostalAddress';
    protected array $properties = [ 'streetAddress', 'addressLocality', 'addressRegion', 'addressCountry', 'postalCode' ];

    public function createSqlTable($type = null): array {
        return parent::createSqlTable("PostalAddress");
    }
}
