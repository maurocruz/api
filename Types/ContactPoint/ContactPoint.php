<?php

namespace Fwc\Api\Type;

/**
 * ContactPoint
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class ContactPoint extends TypeAbstract implements TypeInterface
{
    public function get($args) {
        ;
    }
    
    public function createSqlTable($type = null): bool
    {
        return parent::createSqlTable("ContactPoint");
    }
}
