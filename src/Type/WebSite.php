<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class WebSite extends Entity implements TypeInterface {
    protected $table = 'webSite';
    protected $type = 'WebSite';
    protected $properties = ['name'];
    protected $hasTypes = ['hasPart'=>'WebPage'];

}