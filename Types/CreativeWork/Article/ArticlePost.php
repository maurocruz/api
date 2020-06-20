<?php
namespace fwc\Thing;
/**
 * ArticlePost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class ArticlePost extends ModelPost {
    public $table = "article";

    public function add() {
        $now = date("Y-m-d H:i:s");
        $this->POST['dateCreated'] = $now;
        $this->POST['articleBody'] = addslashes($this->POST['articleBody']);
        if ($this->POST['publishied'] == 1) {
            $this->POST['datePublished'] = $now;
        }
        // insert
        $idArticle = parent::createNewAndReturnLastId($this->POST);
        // sitemap
        $this->sendSitemap($this->POST['publishied']);
        return "/admin/article/edit/$idArticle";
    }
    
    public function edit(): bool { 
        $idarticle = $this->POST['idarticle'];
        unset($this->POST['idarticle']);
        $this->POST['articleBody'] = addslashes($this->POST['articleBody']);
        unset($this->POST['dateModified']);        
        if ($this->POST['publishied'] == '1' && $this->POST['datePublished'] == '') {
            $this->POST['datePublished'] = date("Y-m-d H:i:s");
            
        } elseif ($this->POST['publishied'] === '0') {
            $this->POST['datePublished'] = null;
        }
        // update
        parent::update($this->POST, "idarticle=$idarticle");
        // sitemap
        $this->sendSitemap($this->POST['publishied']);
        return true;
    }
    
    public function erase() {
        $idarticle = $this->POST['idarticle'];
        (new ArticleModel($this->settings))->delete("idarticle=$idarticle");
        // sitemap
        $this->sendSitemap($this->POST['publishied']);
        return "/admin/article";
    }
    
    private function sendSitemap($publishied) {
        if ($this->POST['publishied'] == 1) {
            $data = json_decode((new ArticleGet($this->settings))->listAll(null, "datePublished DESC", 500), true);
            (new \fwc\Helper\Sitemap())->saveSitemapNews($this->settings['sitename'], $this->settings['language'], $data['itemListElement']);
        }
    }
    
    public function createSqlTable($type = null) {
        // require
        $maintenance = (new \fwc\Maintenance\Maintenance($this->settings));
        $maintenance->createSqlTable("ImageObject");
        $maintenance->createSqlTable("Person");
        $maintenance->createSqlTable("Organization");
        // sql create statement
        return parent::createSqlTable("Article");
    }
}
