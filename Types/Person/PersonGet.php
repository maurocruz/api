<?php
/**
 * PersonGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

declare(strict_types=1);

namespace fwc\Thing;

class PersonGet extends ThingGetAbstract implements ThingGetInterface
{
    protected $table = "person";
    protected $type = "Person";
    
    public function index(string $where = null, $orderBy = null, $groupBy = null, $limit = null, $offset = null): string
    {
        return $this->returnListAll( parent::index($where, $orderBy, $groupBy, $limit, $offset) );
    }
    
    
    public function listAll($where = null, $order = null, $limit = null, $offset = null) 
    {
        return parent::listAll($where, $order, $limit, $offset);
    }

    public function selectById($id, $order = null, $field = '*', $complete = null) 
    {
        return parent::selectById($id, $order, $field);
    }
    
    public function getHasPart($tableOwner, $idOwner, $order = null, $groupby = null)
    {
        return parent::getHasPart($tableOwner, $idOwner, $order, $groupby);
    }
    
    public function selectNameByAjax($queryParams) : string 
    {
        $data = parent::selectByName($queryParams['name']);   
        return parent::returnListAll($data, __CLASS__.'::schema');
    }
    
    protected function schema($value) 
    {        
        $this->schema['name'] = $value['name'];

        $this->schema['identifier'] = [
            [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idperson'] ]
        ];
        
        return $this->getSchema($value);
    }
}
