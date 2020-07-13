<?php
namespace fwc\Thing;
/**
 * PropertyValuePost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

class PropertyValuePost extends EditingAbstract {
    
    public function add()     {       
        $idWebPage = $this->POST['idWebPage'];
        unset($this->POST['idWebPage']);        
        // insert bd
        (new PropertyValueModel($this->settings))->addNewPropertyValueWithPartOf($this->tableOwner, $this->idOwner, $this->POST);        
        // save json
        (new WebPagePost($this->settings))->saveJsonWebPage($this->idWebPage);        
        return true;
    }
    
    public function edit() {
        $idPropretyValue = $this->POST['idattributes'];
        unset($this->POST['idattributes']);        
        // update 
        (new PropertyValueModel($this->settings))->update($this->POST, "idattributes=$idPropretyValue");        
        // save json
        (new WebPagePost($this->settings))->saveJsonWebPage($this->idWebPage);        
        return true;        
    }    
    
    public function delete() {        
        (new PropertyValueModel($this->settings))->delete("idattributes=".$this->POST['idattributes']);        
        (new WebPagePost($this->settings))->saveJsonWebPage($this->POST['idWebPage']);        
        return true;
    }
}
