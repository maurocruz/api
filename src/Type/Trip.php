<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Trip extends Entity implements TypeInterface {
    protected $table = 'trip';
    protected $type = 'Trip';
    protected $properties = ['name'];
    protected $hasTypes = ['provider'=>'Organization','image'=>'ImageObject','identifier'=>'PropertyValue','subTrip'=>'Trip'];

}