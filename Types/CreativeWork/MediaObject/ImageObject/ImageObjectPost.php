<?php
namespace fwc\Thing;
/**
 * ImageObjectPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
use fwc\Thing\ImageObjectModel;

class ImageObjectPost extends ModelPost {
    public $table = "imageObject";
    
    public function add(): string {
        
    }
    
    public function edit(): bool {
        // update imageObject
        $idImageObject = $this->POST['idimageObject'];
        parent::update([ "keywords" => $this->POST['keywords']], "`idimageObject`=$idImageObject");
        unset($this->POST['idimageObject']);
        unset($this->POST['keywords']);
        // update tableHas
        parent::editHasPart($this->tableOwner, $this->idOwner, "imageObject", $idImageObject, $this->POST);
        // save jsonWebPage
        if ($this->idWebPage) {
            (new WebPagePost($this->settings))->saveJsonWebPage($this->idWebPage);
        }
        return false;
    }
    
    public function erase() {
        $idimageObject = $this->POST['idimageObject'];
        parent::delete([ "idimageObject" => $idimageObject ]);// save jsonWebPage
        parent::saveJsonPageWeb();
        return true;
    }    
    
    // delete imageIsPartoOf
    public function deleteHasPart() {
        parent::deleteWithHasPart($this->tableOwner, $this->idOwner,"imageObject", $this->POST['idimageObject']);        
        return true;
    }
    
    public function addWithPartOf() {
        // upload
        $filename = $this->uploadImages(); 
        // save images and tableHas
        $this->POST['contentUrl'] = $this->POST['location']."/".$filename;
        unset($this->POST['location']);
        unset($this->POST['group']);
        $idHas = parent::createNewAndReturnLastId($this->POST);
        parent::insertWithHasPart($this->tableOwner, $this->idOwner, "imageObject", $idHas);       
        // save jsonWebPage
        parent::saveJsonPageWeb();
        // redirect
        return true;
    }
    
    public function insertHasPartFromServer() {
        parent::insertNewWithPartOf($this->tableOwner, $this->idOwner, $this->POST);
        return true;
    }

    public function insertHasPartFromDatabase() {
        parent::insertWithHasPart($this->tableOwner, $this->idOwner, "imageObject", $this->POST['idimageObject']);
        // save jsonWebPage
        parent::saveJsonPageWeb();
        return true;
    }
        
    private function uploadImages() {
        $imageUpload = $this->FILE['imageupload'];    
        $location = $this->POST['location'];
        $dir = substr($location, 0, 1) == '/' ? $location : '/'.$location;      
        $filename = self::correctFilename($imageUpload['name']);
        $path = $dir."/".$filename; 
        (new \fwc\Html\Object\ThumbnailObject($imageUpload['tmp_name']))->uploadImage($path);        
        return $filename;
    }    
    
    // TOOLS
    private static function correctFilename($filename) {
        $search = array(' ','á','à','ã','â','é','è','ê','í','ì','î','ó','ò','õ','ô','ú','ù','û','ñ','Á','À','Ã','Â','É','È','Ê','Í','Ì','Î','Ó','Ò','Õ','Ô','Ú','Ù','Û','Ñ','/','(',')');
        $replace = array('','a','a','a','a','e','e','e','i','i','i','o','o','o','o','u','u','u','n','A','A','A','A','E','E','E','I','I','I','O','O','O','O','U','U','U','N','','','');
        return str_replace($search, $replace, $filename);
    }
    
    public function createSqlTable($type = null) {   
        // sql create statement
        return parent::createSqlTable("ImageObject");
    }
}
