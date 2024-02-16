<?php
declare(strict_types=1);
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class WebPage extends Entity
{
    /**
     * @var string
     */
    protected string $table = "webPage";
    /**
     * @var string
     */
    protected string $type = "WebPage";
    /**
     * @var array|string[]
     */
    protected array $properties = [];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "hasPart" => "WebPageElement", "identifier" => "PropertyValue", "isPartOf"=>'WebSite'];
}
