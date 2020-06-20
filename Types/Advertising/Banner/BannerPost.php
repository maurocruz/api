<?php
namespace fwc\Thing;
/**
 * BannerPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

class BannerPost extends ModelPost
{
    protected $table = "banners";
    
    public function add(): string
    {
        parent::created($this->POST);
        return true;
    }
    
    public function edit(): bool {        
        $idcontract = $this->POST['idadvertising'];
        unset($this->POST['idadvertising']);
        
        (new BannerModel())->update($this->POST, "idadvertising=$idcontract");
        
        return true;
    }
    
    public function erase() {
        
    }
}
