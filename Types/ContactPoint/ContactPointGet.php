<?php

/**
 * ContactPointGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

declare(strict_types=1);

namespace fwc\Thing;

class ContactPointGet extends ThingGetAbstract implements ThingGetInterface
{
    protected $table = "contactPoint";
    protected $type = "ContactPoint";
    protected $hasProperties = [ "name", "contactType", "email", "telephone", "whatsapp", "obs", "position" ];
    
    public function index(string $where = null, $orderBy = null, $groupBy = null, $limit = null, $offset = null): string
    {
        return $this->returnListAll( parent::index($where, $orderBy, $groupBy, $limit, $offset) );
    }
    
    
    public function listAll(string $where = null, $order = null, $limit = null, $offset = null) {
        $data = parent::listAll($where, $order, $limit, $offset);
    }
    
    public function getHasPart($tableOwner, $idOwner, $groupby = null, $order = null) 
    {
        return parent::getHasPart($tableOwner, $idOwner, $order, $groupby);
    }

    public function getPublicContactPoint($tableOwner, $idOwner) {
        $data = parent::getHasPart($tableOwner, $idOwner, "position ASC");
        foreach ($data as $value) {
            unset($value['idcontactPoint']);
            $response[] = self::schema($value);
        }        
        return isset($response) ? json_encode($response) : null;
    }

    public function getCompleteContactPointPartOf($tableOwner, $idOwner) {                
        $data = parent::getHasPart($tableOwner, $idOwner, $this->table.".position ASC"); 
        foreach ($data as $value) {
            $response[] = self::schema($value);
        }        
        return isset($response) ? json_encode($response) : null;
    }
    
    protected function schema($value) 
    {   
        $this->setSchema("identifier", [
            [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idcontactPoint'] ],
            [ "@type" => "PropertyValue", "name" => "position", "value" => $value['position'] ]
        ]);
        
        return $this->getSchema($value);
    }
}
