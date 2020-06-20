<?php
namespace fwc\Thing;
/**
 * OfferGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class OfferGet extends ModelGet {
    protected $table = "offer";
    
    public function listAll(string $where = null, $order = null, $limit = null, $offset = null) {
        $data = parent::listAll($where, $order, $limit, $offset);
        if (isset($data['errorInfo'])) {
           return json_encode($data); 
        } else {
            foreach ($data as $key => $value) {
                $list[] = self::offer($value);
            }  
            return json_encode(ItemList::list(count($data),$list ?? null));     
        }
    }
    
    public function selectById($id, $order = null, $field = '*') {
        $data = parent::selectById($id, $order, $field);
        if (empty($data)) {
            return null;
        } else {
            $value = $data[0];
            $classNameItemOffered = "\\fwc\\Thing\\".ucfirst($value['itemOfferedType'])."Get";
            $value['itemOffered'] = json_decode((new $classNameItemOffered())->selectById($value['itemOfferedId']), true);
            $value['eligibleQuantity'] = json_decode((new QuantitativeValueGet())->selectById($value['eligibleQuantity']), true);
            return json_encode(self::offer($value));
        }
    }
    
    public function listAllWithPartOf($tableOwner, $idOwner ) {
        $data = parent::listAllWithPartOf($tableOwner, $idOwner);
        if (empty($data)) {
            return null;
        } else {
            foreach ($data as $key => $value) {      
                $list[] = self::offer($value);
            }  
            return json_encode(ItemList::list(count($data),$list ?? null));  
        }
    }


    public function getHasPart($tableOwner, $idOwner, $order = null, $groupby = null) {
        $data = parent::getHasPart($tableOwner, $idOwner, $order, $groupby);
        if (empty($data)) {
            return null;
        } else {
            foreach ($data as $value) {                
                $value['eligibleQuantity'] = json_decode((new QuantitativeValueGet())->selectById($value['eligibleQuantity']), true);
                $value['itemOffered'] = json_decode((\fwc\Cms\Helper\ClassFactory::createFwcThingClass($value['itemOfferedType']))->selectSimpleById($value['itemOfferedId']), true);
                $itemListElement[] = self::offer($value);
            }
            return json_encode(ItemList::list(count($data), $itemListElement, "Unordered", "List of Offers"));
        }
    }
    
    public function getHasPartForPublic($tableOwner, $idOwner, $order = null, $groupby = null) {
        $data = parent::getHasPart($tableOwner, $idOwner, $order, $groupby);
        if (empty($data)) {
            return null;
        } else {
            $price = 0;            
            foreach ($data as $value) {
                //var_dump($value);
                $price += $value['price'];
                $value['eligibleQuantity'] = json_decode((new QuantitativeValueGet())->selectById($value['eligibleQuantity']), true);
                $value['itemOffered'] = json_decode((\fwc\Cms\Helper\ClassFactory::createFwcThingClass($value['itemOfferedType']))->selectSimpleById($value['itemOfferedId']), true);
            }
            $value['name'] = "Offer of ";
            $value['price'] = $price;
            return json_encode(self::primaryOffer($value));
        }
    }
    
    static private function primaryOffer($value) {
         return [
            "@context" => "https://schema.org",
            "@type" => "Offer",
            "name" => $value['name'],
            "description" => $value['description'],
            "price" => $value['price'],
            "priceCurrency" => $value['priceCurrency'],
            "itemOffered" => $value['itemOffered'] ?? null,
            "eligibleQuantity" => $value['eligibleQuantity'],
            "leaseLenght" => $value['leaseLenght'],
            "inventoryLevel" => $value['inventoryLevel'],
            "validThrough" => $value['validThrough']
        ];
    }

    static private function offer($value) {
        $schema = self::primaryOffer($value);
        $schema['identifier'] = [
            [ "@type" => "PropertyValue", "name" => "ID", "value" => $value['idoffer']],
            [ "@type" => "PropertyValue", "name" => "quantity", "value" => $value['quantity']]
        ];
        $schema['priceSpecification'] = $value['priceSpecification'];
        $schema['priceValidUntil'] = $value['priceValidUntil'];
        $schema['itemCondition '] = $value['itemCondition'];
        return $schema;
    }
    
    public function selectNameByAjax($param) {
        $name = $param['name'];
        $query = "SELECT offer.idoffer, CONCAT(service.`name`, ' - ', offer.`name`) AS name "
                . "FROM offer, service  "
                . "WHERE (offer.`name` LIKE '%$name%' OR service.`name` LIKE '%$name%') "
                . "AND offer.itemOfferedId = service.idservice "
                . "ORDER BY offer.`name`, service.`name`;";
        
        $data =  parent::getQuery($query);
        return parent::listNameAndId($data);
    }
}
