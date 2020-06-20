<?php

namespace fwc\Thing;

/**
 * ArticleModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

class ArticleModel extends Crud {
    public $table = "article";
      
    public function __construct() {
        parent::__construct();
    }
    
    public function countRows($where = null) 
    {
        $query = "SELECT COUNT(*) AS q FROM $this->table {$where}";
        $return = parent::getQuery($query);
        return $return[0]['q'];
    }
    
    public function listNews($limit, $termo = null, $publishied = 1) 
    {
        $query = "SELECT * FROM $this->table";
        $query .= $publishied == 1 || $termo ? " WHERE" : null;
        $query .= $publishied == 1 ? " publishied=1" : null;
        $query .= $termo ? " AND (`name` LIKE '% {$termo} %' OR `articleBody` LIKE '% {$termo} %') " : null;             
        $query .= " ORDER BY datePublished DESC";
        $query .= " LIMIT $limit";
        $query .= ";";
        return parent::getQuery($query);
    }
    
    public function getArticle($name, $datePublished = null) 
    {
        $title = urldecode($name);        
        $query = "SELECT article.* FROM $this->table WHERE article.headline='{$title}'";        
        if ($datePublished) {
            $query .= " AND substr(article.datePublished,1,10)='{$datePublished}'";
        }        
        $query .= ";"; 
       return parent::getQuery($query);
    }
    
    public function getArticleById($identifier) 
    {
        $query = "SELECT * FROM $this->table WHERE idarticle=$identifier;";
        return parent::getQuery($query);
    }
    
    public function getImages($idOwner) 
    {
        return parent::getItemsWithPartOf($idOwner, "images");
    }
    
    public function addNew($data) 
    {
        print parent::insert($data);
        return parent::lastInsertId();
    }
}
