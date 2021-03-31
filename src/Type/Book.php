<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Book extends Entity implements TypeInterface {
    protected $table = "book";
    protected $type = "Book";
    protected $properties = [ "name", "author" ];
    protected $hasTypes = [ ];
    
    public function createSqlTable($type = null): array {
        return parent::createSqlTable("Book");
    }
}
