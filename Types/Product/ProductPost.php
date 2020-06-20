<?php

namespace fwc\Thing;

/**
 * ProductPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class ProductPost extends EditingAbstract
{
    public function edit() 
    {
        $idproduct = $this->POST['idproduct'];
        unset($this->POST['idproduct']);
        unset($this->POST['status']);
                
        (new ProductModel($this->settings))->update($this->POST, "idproduct=$idproduct");
        
        return true;
    }   
    
    public function add() 
    {        
        $idproduct = (new ProductModel($this->settings))->insert($this->POST);
        
        return "/admin/modules/product/".$idproduct;
    }
    
    public function delete() {
        
    }
}
