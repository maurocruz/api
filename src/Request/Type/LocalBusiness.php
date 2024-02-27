<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\Schema\Schema;

class LocalBusiness extends Entity
{
    /**
     * @var string
     */
    protected string $table = "localBusiness";
    /**
     * @var string
     */
    protected string $type = "LocalBusiness";
    /**
     * @var array|string[]
     */
    protected array $properties = [];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "location" => "Place", "organization" => "Organization", "contactPoint" => "ContactPoint", "member" => "Person", "image" => "ImageObject" ];

    /**
     * @param array $params
     * @return array
     */
    public function post(array $params): array
    {
        $params['dateCreated'] = date("Y-m-d");
        return parent::post($params);
    }

    /**
     * @param $params
     * @param $data
     * @return array
     */
    public function buildSchema($params, $data): array
    {
        return (new Schema($this->type, $this->properties, $this->hasTypes))->buildSchema($params, $data);
    }
}
