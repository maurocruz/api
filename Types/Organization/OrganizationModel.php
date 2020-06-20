<?php
namespace fwc\Thing;
/**
 * OrganizationModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class OrganizationModel extends \fwc\Api\Crud {
    public $table = "organization";
    
    public function __construct($settings) {
        parent::__construct($settings['pdo']);
    }
    
    public function getByIdOrganization($idOrganization) {
        $query = "SELECT * FROM $this->table WHERE idOrganization = $idOrganization;";
        return parent::getQuery($query);
    }
    
    public function getSimpleList($search = null, $order = null, $limit = null, $offset = null) {
        $query = "SELECT * FROM $this->table";
        $query .= $search ? " WHERE name LIKE '%$search%'" : null;
        $query .= $order ? " ORDER BY $order" : null;
        $query .= ";";        
        return parent::getQuery($query);
    }
    
    public function install() {
        $query = file_get_contents(__DIR__.'/install.sql');
        return parent::getQuery($query);
    }
    
    /*
    public function transfer() { // provisory
        $this->settings['database'] = 'pirenopolis03';
        $query = "SELECT * FROM pirenopolis03.clientes WHERE cnpj is not null AND cnpj != '' AND length(cnpj) > 15;";
        $data = parent::getQuery($query);
        
        $this->settings['database'] = 'pirenopolis02';
        
        foreach ($data as $key => $value) {
            $organization['name'] = addslashes($value['nome']);
            $organization['description'] = $value['descricao'] != '' ? addslashes($value['descricao']) : null;
            $organization['legalName'] = addslashes($value['razaosocial']);
            $organization['taxId'] = $value['cnpj'];
            $organization['url'] = $value['site'];
            $organization['hasOfferCatalog'] = $value['tags'];
            $organization['create_time'] = $value['create_time'] ?? date("Y-m-d H:i:s");
            $organization['update_time'] = $value['update_time'];

            $postalAddress['streetAddress'] = addslashes($value['logradouro'] . ($value['numero'] ? ", ".$value['numero'] : null) . ($value['complemento'] ? ", ".$value['complemento'] : null) . ($value['bairro'] ? ", ".$value['bairro'] : null));
            $postalAddress['addressLocality'] = addslashes(trim($value['cidade']));
            $postalAddress['addressRegion'] = $value['uf'];

            $contactPoint['telephone'] = $value['telefones'] != '' ? trim($value['telefones']) : null;
            $contactPoint['email'] = $value['email'] != '' ? trim($value['email']) : null;
            
            $resp = trim($value['responsavel']);
            $person['givenName'] = strstr($resp, " ", true);
            $person['familyName'] = substr(strstr($resp, " "),1);
            
            // add postal address 
            //var_dump($postalAddress);
            $idPostalAddress = (new PostalAddressModel($this->settings))->insertNew($postalAddress);
            // add organization
            //var_dump($organization);
            $organization['address'] = $idPostalAddress;
            $idOrganization = (new OrganizationModel($this->settings))->insertNew($organization);
            // add contact point
            if ($contactPoint['telephone'] == true || $contactPoint['email'] == true) {
                //var_dump($contactPoint);
                (new ContactPointModel($this->settings))->insertNewWithPartOf('Organization', $idOrganization, $contactPoint);
            }
            // add person
            if ($person['givenName'] == true) {
                $person['dateRegistration'] = date("Y-m-d H:i:s");
                //var_dump($person); 
                (new PersonModel($this->settings))->insertNewWithPartOf('Organization', $idOrganization, $person);
            }
        }
    }*/
}
