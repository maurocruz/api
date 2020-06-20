<?php
namespace fwc\Thing;

/**
 * WebPageEditing
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

class WebPagePost extends ModelPost {
    
    public function add() {
        $this->POST['description'] = addslashes($this->POST['description']);
        $this->POST['dateCreated'] = date('Y-m-d H:i:s');
        $idWebpage = (new WebPageModel($this->settings))->insertNewWebpage($this->POST);
        return "/admin/webSite/webPage/edit/$idWebpage";
    }

    public function edit(): bool {            
        $model = new WebPageModel($this->settings);        
        $this->POST['dateModified'] = date("Y-m-d H:i:s");        
        // salva alterações no banco de dados
        $model->update($this->POST, "idwebPage=".$this->idWebPage);        
        // salva jsonwebpage
        $this->saveJsonWebPage($this->idWebPage);        
        return true;
    }

    public function erase() {
        
    }
    
    public function saveJsonWebPage($data) {
        if (is_array($data)) {
            $idwebPage = $data['identifier'];
            $jsonld = json_encode($data);                    
        } elseif (is_numeric($data)) {
            $idwebPage = $data;
            $jsonld = (new WebPageGet($this->settings))->getPageById($idwebPage);
        }                
        $json = str_replace(["\\r","\\n","\\t"], " ", $jsonld);
        (new WebPageModel($this->settings))->update(["jsonwebpage" => $json ], "idwebPage=$idwebPage");
    }
}
