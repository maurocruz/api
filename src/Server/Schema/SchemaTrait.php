<?php
declare(strict_types=1);
namespace Plinct\Api\Server\Schema;

class SchemaTrait extends  SchemaAbstract
{
  /**
   * @param array $data
   */
  protected function listSchema(array $data)
  {
    $listItem=[];
    $this->schema['@context'] = $this->context;
    $this->schema['@type'] = "ItemList";
    $this->schema['numberOfItems'] = count($data);
    $this->schema['itemListOrder'] = $this->params['ordering'] ?? 'ascending';
    if (!empty($data)) {
      foreach ($data as $key => $value) {
        $item['@type'] = "ListItem";
        $item['position'] = ($key + 1);
        $item['item'] = self::newSchema($value);
        $listItem[] = $item;
      }
    }
    $this->schema["itemListElement"] = $listItem;
  }

  /**
   * @param array $data
   * @return array|null
   */
  protected function newSchema(array $data): ?array
  {
    $this->idHasPart = $data['id'] ?? $data["id$this->tableHasPart"] ?? null;
    // SCHEMA WRITE
    $schema = new SchemaWrite($this->context, $this->type);
    // ADD SELECTED PROPERTIES
    foreach ($data as $property => $valueProperty) {
        $schema->addProperty($property, $valueProperty);
    }
    // RELATIONSHIP IS PART OF
    foreach ($this->hasTypes as $propertyIsPartOf => $tableIsPartOf) {
	    $params = [];
      // IF TYPE IS PART OF IS DEFINED WITH FIELD TYPE
      if ($tableIsPartOf === true) {
        $tableIsPartOf = $data[$propertyIsPartOf.'Type'];
      }
      if ($tableIsPartOf) {
        // GET OBJECT TYPE PART OF
        $className = "Plinct\\Api\\Type\\" . ucfirst($tableIsPartOf);
        if (class_exists($className)) {
          $class = new $className();
					// if attached propety exists
					if (isset($this->attachedProperty[$propertyIsPartOf])) {
						$params = ['properties'=>$this->attachedProperty[$propertyIsPartOf]];
					}
          // RELATIONSHIP ONE TO ONE
          if (self::ifExistsColumn($propertyIsPartOf)) {
            if (isset($data[$propertyIsPartOf])) {
							$params['id'] = $data[$propertyIsPartOf];
              $dataIsPartOf = $class->get($params);
              $schema->addProperty($propertyIsPartOf, $dataIsPartOf[0] ?? null);
            }
          } // RELATIONSHIP ONE TO MANY
          else {
            if ($tableIsPartOf == "Offer") {
              $params['itemOfferedType'] = $this->tableHasPart;
							$params['itemOffered'] = $this->idHasPart;
            } elseif ($tableIsPartOf == "Invoice" || $tableIsPartOf == 'OrderItem') {
              $params['referencesOrder'] = $this->idHasPart;
							unset($params['id']);
            } elseif (isset($class->getHasType()['isPartOf']) && $class->getHasType()['isPartOf'] == ucfirst($this->tableHasPart)) {
              $params['isPartOf'] = $this->idHasPart;
            } else {
              $params['tableHasPart'] = $this->tableHasPart;
							$params['idHasPart'] = $this->idHasPart;
            }
            $dataIsPartOf = $class->get($params);
            if (empty($dataIsPartOf) || !isset($dataIsPartOf[0])) $dataIsPartOf = null;
            $schema->addProperty($propertyIsPartOf, $dataIsPartOf);
          }
        }
      }
    }
    // IDENTIFIER
    if ($this->idHasPart) {
      $schema->addProperty('identifier', ["@type"=>"PropertyValue", "name" => "id", "value" => $this->idHasPart]);
    }
    return $schema->ready();
  }
}
