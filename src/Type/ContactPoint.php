<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class ContactPoint extends Entity implements TypeInterface {
    protected string $table = 'contactPoint';
    protected string $type = 'ContactPoint';
    protected array $properties = [ "name", "telephone", "email", "whatsapp", "contactType", "position", "obs" ];

    public function createSqlTable($type = null): array {
        return parent::createSqlTable("ContactPoint");
    }
}
