<?php
namespace fwc\Thing;
/**
 * ServiceGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class ServiceGet extends ModelGet 
{
    protected $table = "service";
    
    public function listAll(string $where = null, $order = null, $limit = null, $offset = null) 
    {
        $data = parent::listAll($where, $order, $limit, $offset);
        if (empty($data)) {
            return json_encode(ItemList::list());
        } else {
            foreach ($data as $value) {
                $value['provider'] = json_decode((\fwc\Cms\Helper\ClassFactory::createFwcThingClass($value['providerType']))->selectById($value['providerId']), true);
                $items[] = self::service($value);
            }            
            return json_encode(ItemList::list(count($data), $items, "Unordered", "List os services"));
        }
    }
    
    public function selectById($id, $order = null, $field = '*') {
        $data = parent::selectById($id, $order, $field);
        if (empty($data)) {
            return json_encode($data);
        } else {
            $value = $data[0];          
            // provider
            $providerClass = "fwc\\Thing\\".ucfirst($value['providerType'])."Get";
            $value['provider'] = json_decode((new $providerClass())->getSimpleById($value['providerId']), true);
            // offers
            $value['hasOfferCatalog'] = json_decode((new OfferGet())->getHasPart("service", $value['idservice']), true);
            return json_encode(self::service($value));
        }
    }

    public function selectSimpleById($id, $order = null, $field = '*')
    {
        $data = parent::selectById($id, $order, $field);
        if (empty($data)) {
            return json_encode($data);
        } else {
            $value = $data[0];          
            // provider
            $value['provider'] = json_decode((\fwc\Cms\Helper\ClassFactory::createFwcThingClass($value['providerType']))->selectById($value['providerId']), true);            
            return json_encode(self::service($value));
        }
    }    

    public function getOfProvider($tableOwner, $idOwner) {
        $data = (new ServiceModel())->listServicesOfProvider($tableOwner, $idOwner);
        if (empty($data)) {
            return json_encode(ItemList::list(0, null, null, ucfirst($tableOwner)." services", "offerCatalog"));
        } else {
            foreach ($data as $value) {
                // provider
                $providerClass = "fwc\\Thing\\".ucfirst($value['providerType'])."Get";
                $value['provider'] = json_decode((new $providerClass())->getSimpleById($value['providerId']), true);
                // hasOffercatalog
                $value['hasOfferCatalog'] = json_decode((new OfferGet())->getHasPart("service", $value['idservice']), true);
                $items[] = self::service($value);
            }
            return json_encode(ItemList::list(count($data), $items, "Unordered", "Services of ".ucfirst($tableOwner), "OfferCatalog"));
        }
    }
    
    static private function service($value) {
        return [
            "@context" => "https://schema.org",
            "@type" => "Service",
            "identifier" => [
                [ "@type" => "PropertyValue", "name" => "ID", "value" => $value['idservice'] ]
            ],
            "name" => $value['name'] ?? null,
            "description" => $value['description'],
            "provider" => $value['provider'] ?? null,
            "audience" => $value['audience'] ?? null,
            "serviceType" => $value['serviceType'] ?? null,
            "serviceOutput" => $value['serviceOutput'] ?? null,
            "termsOfService" => $value['termsOfService'] ?? null,
            "hasOfferCatalog" => $value['hasOfferCatalog'] ?? null
        ];
    }
}
