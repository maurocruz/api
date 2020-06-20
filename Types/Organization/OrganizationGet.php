<?php

/**
 * OrganizationGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

namespace fwc\Thing;

class OrganizationGet extends ThingGetAbstract implements ThingGetInterface 
{
    protected $table = "organization";
    protected $type = "Organization";
    
    public function index(string $where = null, $orderBy = null, $groupBy = null, $limit = null, $offset = null): string
    {
        return $this->returnListAll( parent::index($where, $orderBy, $groupBy, $limit, $offset) );
    }
    
    
    
    // ITEM LIST
    public function listAll($where = null, $order = null, $limit = null, $offset = null) 
    {
        return parent::listAll($where, $order, $limit, $offset);
    }
        
    public function selectById($idorganization, $order = null, $field = '*') 
    {
        return parent::selectById($idorganization, $order, $field);
    }
    
    public function getSimpleList($search = null, $order = null, $limit = null, $offset = null) 
    {
        $data = (new OrganizationModel())->getSimpleList($search, $order, $limit, $offset);
        
        if (array_key_exists('errorInfo',$data)) { // MAINTENANCE
            $return = (new \fwc\Maintenance\Maintenance())->errorSql($data['errorInfo']);
            
        } elseif (empty($data)) { // NOT FOUNDED
            $return = ItemList::list(0, null);
            
        } else { // ITEM LIST
            foreach ($data as $value) {            
                $list[] = self::schema($value);
            }
            
            $return = ItemList::list(count($data), $list, "ItemListOrderAscending");
        }
        
        return json_encode($return);  
    }
    
    public function getSimpleById($id) 
    {
        $data = parent::selectById($id);
        return json_encode(self::organization($data[0]));
    }
    
    public function getServiceByIdorganization($idorganization) 
    {
        // data of organization
        $data = parent::selectById($idorganization);
        $value = $data[0];
        // services data
        $listService = json_decode((new ServiceGet())->listServicesOfProvider($idorganization, "organization"), true);
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "Organization",
            "name" => $value['name'],
            "identifier" => [
                [ "@type" => "PropertyValue", "name" => "ID", "value" => $value['idorganization'] ]
            ],
            "hasOffercatalog" => $listService
        ];
        return json_encode($schema);
    }
    
    public function selectNameByAjax($queryParams) 
    {
        return parent::selectByAjax("Organization", "idorganization", $queryParams);
    }
    
    protected function schema($value) 
    {
        $this->schema['additionalType'] = $value['additionalType'];
        $this->setSchema("legalName",$value['legalName']);
        $this->setSchema("name",$value['name']);
        $this->setSchema("url",$value['url']);
        $this->setSchema("identifier",[
                [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idorganization'] ]
            ]);
        
        return $this->getSchema($value);
    }
    
}
