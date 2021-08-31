<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class Article extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected $table = "article";
    /**
     * @var string
     */
    protected string $type = "Article";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "headline", "datePublished" ];
    /**
     * @var array
     */
    protected array $hasTypes = [ "image" => "ImageObject", "author" => "Person", "publisher" => true ];

    /**
     * @param array $params
     * @return array
     */
    public function post(array $params): array
    {
        $params['dateCreated'] = date("Y-m-d H:i:s");
        $params['datePublished'] = $params['publishied'] == '1' ? date("Y-m-d H:i:s") : null;
        return parent::post($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function put(array $params): array
    {
        if (isset($params['datePublished'])) {
            $params['datePublished'] =
                $params['publishied'] == '1' && $params['datePublished'] == ''
                    ? date("Y-m-d H:i:s")
                    : ( $params['publishied'] == '0' ? null : $params['datePublished'] );
        }

        return parent::put($params);
    }

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        $maintenance = new Maintenance();
        $maintenance->createSqlTable("Person");
        $maintenance->createSqlTable("ImageObject");
        $maintenance->createSqlTable("Organization");
        return parent::createSqlTable("Article");
    }
}
