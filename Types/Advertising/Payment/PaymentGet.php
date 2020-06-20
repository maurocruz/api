<?php
namespace fwc\Thing;

/**
 * paymentsGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class PaymentGet 
{
    public function getpaymentByIdcontract($idcontract) 
    {
        return json_encode((new PaymentModel())->getPaymentsByIdContract($idcontract));
    }
    
    public function getAll($order) 
    {
        $data = (new PaymentModel())->getAll($order);        
        foreach ($data as $value) {
            $value['hasPartOf'] = $value['idadvertising'] ? json_decode((new AdvertisingGet())->getCustomerByIdContract($value['idadvertising']), true) : null;
            $list[] = $value;
        }        
        $return = [
            "@type" => "ItemList",
            "itemListOrder" => "ItemListUnordered",
            "numberOfItems" => count($data),
            "itemListElement" => $list
        ];        
        return json_encode($return);
    }
    
    public function getSimpleList($order = null, $period = null) 
    {
        $data = (new PaymentModel())->getsimpleList($order, $period);
                
        $return = [
            "@type" => "ItemList",
            "itemListOrder" => "ItemListUnordered",
            "numberOfItems" => count($data),
            "itemListElement" => $data
        ];
        
        return json_encode($return);
        
    }
      
}
