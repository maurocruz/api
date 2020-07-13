<?php
namespace fwc\Thing;
/**
 * PropertyValueGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class PropertyValueGet extends ModelGet {
    protected $table = "attributes";
    
    public function getPropertyValue($tableOwner, $idOwner) {
        $data = (new PropertyValueModel($this->settings))->getPropertyValueIsPartOf($tableOwner, $idOwner);        
        foreach ($data as $value) {
            $properties[] = self::propertyValue($value['name'], $value['value']);
        }        
        return $properties ?? null;
    }
    
    static public function propertyValue($name, $value) {
        return [ "@type" => "PropertyValue", "name" => $name, "value" => $value ];
    }
    
    static public function getValue($array, $name) {
        if ($array) {
            foreach ($array as $value) {
                if ($value['name'] == $name) {
                    return $value['value'] ?? $value['result'] ?? $value['description'] ?? null;
                }
            }
        }
    }
    
    static public function setPropertyValue(array $propertiesValues) {
        foreach ($propertiesValues as $name => $value ) {
            $properties[] = self::propertyValue($name, $value);
        }
        return $properties;
    }
    
    public function getPropertyValueWithStrings($tableOwner, $idOwner) {
        $data = parent::getHasPart($tableOwner, $idOwner);
        foreach ($data as $value) {
            $properties[] = '{"idattributes":"'.$value['idattributes'].'", "name":"'.$value['name'].'", "value":"'.$value['value'].'"}';
        };
        return isset($properties) ? "[ ". implode(",", $properties) ."]" : null;
    }
}
