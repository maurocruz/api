<?php
namespace fwc\Thing;
/**
 * QuantitativeValueGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class QuantitativeValueGet extends ModelGet {
    protected $table = "quantitativeValue";
    
    public function selectById($id, $order = null, $field = '*') {
        $data = parent::selectById($id, $order, $field);
        if (empty($data)) {
            return null;
        } else {
            $value = $data[0];
            return json_encode(self::quantatativeValue($value));
        }
    }
    
    static private function quantatativeValue($value) {
        return [
            "@context" => "https://schema.org",
            "value" => $value['value'],
            "minValue" => $value['minValue'],
            "maxValue" => $value['maxValue'],
            "unitText" => $value['unitText'],
            "unitCode" => $value['unitCode']
        ];
    }
}
