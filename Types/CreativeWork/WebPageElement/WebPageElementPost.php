<?php
namespace fwc\Thing;

/**
 * WebPageElementPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

class WebPageElementPost extends EditingAbstract {
    
    public function add() {
        $this->POST['name'] = addslashes($this->POST['name']);
        $this->POST['text'] = addslashes($this->POST['text']);
        $this->POST['dateCreated'] = date('Y-m-d H:i:s');        
        (new WebPageElementModel($this->settings))->addNew($this->idOwner, $this->POST);        
        // updats jsonWebPage
        (new WebPagePost($this->settings))->saveJsonWebPage($this->idOwner);        
        return true;
    }
    
    public function edit() {
        $idWebPageElement = $this->POST['idwebPageElement'];
        unset($this->POST['idwebPageElement']);
        $this->POST['name'] = addslashes($this->POST['name']);
        $this->POST['text'] = addslashes($this->POST['text']);        
        // update post
        (new WebPageElementModel($this->settings))->update($this->POST, "idwebPageElement=$idWebPageElement");        
        // updats jsonWebPage
        (new WebPagePost($this->settings))->saveJsonWebPage($this->idOwner);        
        return true;
    }
    
    public function delete() {
        $idwebPageElement = $this->POST['idwebPageElement'];
        (new WebPageElementModel($this->settings))->delete("idwebPageElement=$idwebPageElement");       
        // updats jsonWebPage
        (new WebPagePost($this->settings))->saveJsonWebPage($this->idOwner);        
        return true;
    }
}
