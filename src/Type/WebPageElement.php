<?php
declare(strict_types=1);
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class WebPageElement extends Entity
{
    /**
     * @var string
     */
    protected string $table = "webPageElement";
    /**
     * @var string
     */
    protected string $type = "WebPageElement";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "name", "text", "position", "image", "identifier" ];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "image" => "ImageObject", "identifier" => "PropertyValue", "isPartOf" => "WebPage" ];

    /**
     * @param array $params
     * @return array
     */
    public function get(array $params = []): array
    {
        $params['orderBy'] = $params['orderBy'] ?? "position";
        return parent::get($params);
    }
}
