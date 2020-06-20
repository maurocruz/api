<?php

/**
 * AdvertsGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

namespace fwc\Thing;

class AdvertisingGet extends ModelGet
{    
    public $table = "advertising";
    
    public function listAll(string $where = null, $order = null, $limit = null, $offset = null) {        
        $data = [];
        $localBusiness = json_decode((new \fwc\Thing\LocalBusinessGet())->listAll($where), true); 
        if ($localBusiness['numberOfItems'] > 0) {
            foreach ($localBusiness['itemListElement'] as $valueLocalBusiness) {
                $item = $valueLocalBusiness['item'];
                $idLocalBusiness = \fwc\Thing\PropertyValueGet::getValue($item['identifier'], "fwc_id");
                $itemData = (new AdvertisingModel())->getContractsByIdLocalBusines($idLocalBusiness);
                if (!empty($itemData)) {
                    foreach ($itemData as $valueItem) {
                        $data[] = $valueItem;
                    }
                }
            }
            // sort            
            usort($data, function($a, $b){ return strcmp($b["vencimento"], $a["vencimento"]); } );
        }
                   
        // get business
        foreach ($data as $value) {
            $business = $value['idlocalBusiness'] ? json_decode((new \fwc\Thing\LocalBusinessGet())->selectById($value['idlocalBusiness']), true) : [ "name" => "undefined" ];
            $value['name'] = $business['name'];
            $list[] = $value;
        }
        $return = [
            "@type" => "ItemList",
            "itemListOrder" => "ItemListUnordered",
            "numberOfItems" => count($data),
            "itemListElement" => $list ?? null
        ];
        return json_encode($return);
    }
    
    public function getContractsExpired($dateLimit = null) 
    {
        $data = (new AdvertisingModel())->getContractsExpired($dateLimit);
        
        $return = [
            "@type" => "ItemList",
            "numberOfItems" => count($data),
            "itemListElement" => $data
        ];        
        return json_encode($return);
    }
    
    public function getDataForNewAd() 
    {
        $contractTypes = (new AdvertisingModel())->getallContractType();
        $localBusiness = (new \fwc\Thing\LocalBusinessModel())->getAllWithCustomer("name ASC");
        
        $return['contractTypes'] = $contractTypes;
        $return['localBusiness'] = $localBusiness;
        
        return json_encode($return);    
    }
    
    public function getCustomerByIdContract($idcontract) 
    {
        $data = (new AdvertisingModel())->getCustomerByIdContract($idcontract);
        if (!empty($data)) {
            $value = $data[0];
            $value['own'] = json_decode((new \fwc\Thing\LocalBusinessGet())->selectById($value['idlocalBusiness']), true);
            $value['payments'] = json_decode((new PaymentGet())->getpaymentByIdcontract($value['idadvertising']), true); 
            $value['history'] = json_decode((new HistoryGet())->getHistoryWithPartOf("advertising", $value['idadvertising'], "datetime desc"), true);
            if ($value['idcontratostipo'] == 4) {
                $value['banner'] = json_decode((new BannerGet())->getBannerByIdcontract($value['idadvertising']), true);
            }
            $value['contractTypes'] = (new AdvertisingModel())->getallContractType();
        }
        return isset($value) ? json_encode($value) : null;
    }
    
    public function selectByIdAndName($id, $name = null) 
    {
        $data = (new AdvertisingModel())->getActivedCustomerById($id);
        
        if (!empty($data)) {
            $id = $data[0]['idlocalBusiness'];
            
            $get = new \fwc\Thing\LocalBusinessGet();
            $get->setProperties("hasOfferCatalog");
            $get->setTypes("ImageObject", "PostalAddress", "ContactPoint");
            
            return $get->selectById($id);
        }        
    }
/*
    public function getAllActivedCustomer() {
        $data = (new AdvertisingModel())->getAllActivedCustomer();        
        $list = self::getListLocalBusiness($data);        
        // ordering list
        shuffle($list);        
        $return = [
            "@type" => "ItemList",
            "itemListOrder" => "ItemListUnordered",
            "numberOfItems" => count($data),
            "itemListElement" => $list
        ];        
        return json_encode($return);
    }
    
    */
    /* 
    
    private function getListLocalBusiness($dataContratos, $search = null) { 
        foreach ($dataContratos as $value) {
            if (isset($value['idlocalBusiness'])) {
                $data = json_decode((new \fwc\Thing\LocalBusinessGet())->getById($value['idlocalBusiness'], false, $search), true); 
                if ($data) {
                    $list[] = $data;
                }
            }
        }
        return $list ?? null;
    }*/
}
