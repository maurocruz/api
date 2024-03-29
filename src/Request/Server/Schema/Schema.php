<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server\Schema;

class Schema extends SchemaTrait
{
	/**
	 * @param $table
	 * @param $properties
	 * @param $hasTypes
	 */
  public function __construct($table, $properties, $hasTypes)
  {
    $this->type = ucfirst($table);
    $this->properties = $properties;
    $this->hasTypes = $hasTypes;
  }

  /**
   * @param array $params
   * @param array $data
   * @return array
   */
  public function buildSchema(array $params,array $data): array
  {
    // VARS
    $this->tableHasPart = lcfirst($this->type);
    $this->params = $params;
    $paramsProperties = $this->params['properties'] ?? null;
    // SET PROPERTIES
    if ($paramsProperties) {
      parent::setProperties($paramsProperties);
    }
    // SET HAS TYPES
    parent::setHasTypes();
    // IF FORMAT ITEM LIST
    if (isset($params['format']) && $params['format'] == 'ItemList') {
      parent::listSchema($data);
    } else {
      foreach ($data as $value) {
        $this->schema[] = parent::newSchema($value);
      }
    }
    return $this->schema;
  }
}
