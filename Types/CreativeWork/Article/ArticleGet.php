<?php

/**
 * ArticleGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

declare(strict_types=1);

namespace fwc\Thing;

class ArticleGet extends ThingGetAbstract implements ThingGetInterface
{
    public $table = "article";
    protected $type = "Article";
    protected $hasProperties = [ "headline", "position", "datePublished", "articleBody" ];
    
    
    public function index(string $where = null, $order = null, $groupBy = null, $limit = null, $offset = null): string
    {
        parent::index($where, $order, $groupBy, $limit, $offset);
    }


    public function listAll($where = null, $order = null, $limit = null, $offset = null) 
    {
        $data = json_decode(parent::listAll($where, $order, $limit, $offset));
        $numberOfItem = parent::read("COUNT(*) AS q");
        $data->numberOfItems = $numberOfItem[0]['q'];
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        
    }
    
    public function getArticle($datePublished, $name) 
    {        
        $data = (new ArticleModel())->getArticle($name, $datePublished == "noticia" ? null : $datePublished);         
        
        if (empty($data)) {
            return null;
        } else {
            $value = $data[0];
            $value['image'] = json_decode((new ImageObjectGet())->getHasPart("article", $value['idarticle']), true);
            $value['publisher'] = json_decode((new OrganizationGet())->selectById($value['publisher']), true);
            $value['author'] = json_decode((new PersonGet())->selectById($value['author']), true);
            return json_encode(self::schema($value), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }
    
    public function selectById($id, $order = null, $field = '*')
    {
        return parent::selectById($id, $order, $field);
    }
    
    protected function schema($value) 
    {
        $this->setSchema("identifier", [
            [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idarticle'] ]
        ]);
        
        $this->getSchema($value);
        
        return $this->schema;
    } 
}
