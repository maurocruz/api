<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Article extends Entity implements TypeInterface {
    protected $table = "article";
    protected $type = "Article";
    protected $properties = [ "headline", "datePublished" ];
    protected $hasTypes = [ "image" => "ImageObject", "author" => "Person", "publisher" => true ];

    public function post(array $params): array {
        $params['dateCreated'] = date("Y-m-d H:i:s");
        $params['datePublished'] = $params['publishied'] == '1' ? date("Y-m-d H:i:s") : null;
        return parent::post($params);
    }
    
    public function put(array $params): array {
        if (isset($params['datePublished'])) {
            $params['datePublished'] =
                $params['publishied'] == '1' && $params['datePublished'] == ''
                    ? date("Y-m-d H:i:s")
                    : ( $params['publishied'] == '0' ? null : $params['datePublished'] );
        }
        return parent::put($params);
    }

    public function createSqlTable($type = null): array {
        $maintenance = new Maintenance();
        $maintenance->createSqlTable("Person");
        $maintenance->createSqlTable("ImageObject");
        $maintenance->createSqlTable("Organization");
        return parent::createSqlTable("Article");
    }
}
