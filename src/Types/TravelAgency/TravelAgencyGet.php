<?php
namespace fwc\Thing;
/**
 * TravelAgencyGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class TravelAgencyGet extends ModelGet implements ThingGetInterface {
    protected $table = "localBusiness";
    
    public function index(string $where = null, $order = null, $groupBy = null, $limit = null, $offset = null): string {
        parent::index($where, $order, $groupBy, $limit, $offset);
    }
    
    public function listAll($where = null, $order = null, $limit = null, $offset = null) {
        return (new LocalBusinessGet())->listAll($where, $order, $limit, $offset);
    }
    
    public function selectById($id, $order = null, $field = '*') {
        return (new LocalBusinessGet())->selectById($id);
    }
    
    /*public function getSimpleById($id, $order = null, $field = '*') {
        return (new LocalBusinessGet())->getSimpleById($id);
    }*/
    
    public function selectIdAndName($id) {
        return (new LocalBusinessGet())->selectById($id);
    }
}
