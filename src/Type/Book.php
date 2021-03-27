<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Book extends Entity implements TypeInterface {
    protected string $table = "book";
    protected string $type = "Book";
    protected array $properties = [ "name", "author" ];
    protected array $hasTypes = [ ];
    
    public function createSqlTable($type = null): array {
        return parent::createSqlTable("Book");
    }
}
