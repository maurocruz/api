<?php

declare(strict_types=1);

namespace Plinct\Api\Response\Format\ClassHierarchy;

use Plinct\Api\ApiFactory;
use Plinct\Soloine\Factory\SoloineFactory;

class ClassHierarchy implements ClassHierarchyInterface
{
  /**
   * @var string
   */
  private string $type;
  /**
   * @var ?string
   */
  private ?string $subClass;

  /**
   * @param string $type
   * @param array $params
   */
  public function __construct(string $type, array $params) {
    $this->type = $type;
    $this->subClass = isset($params['subClass']) && $params['subClass'] != '' ? $params['subClass']
        : (isset($params['class']) && $params['class'] != '' ? $params['class'] : $type);
  }

  /**
   * @return array|null
   */
  private function getSections(): ?array
  {
    // SOLOINE
    $paramsSoloine = ['class' => $this->subClass,'format'=>'hierarchyText','subClass'=>true ];
    $dataSoloine = json_decode(SoloineFactory::schemaorg($paramsSoloine), true);

    // PLINCT
    $paramsPlinct = ['groupBy' => 'additionalType', 'orderBy' => 'dateModified desc'];
    $dataPlinct = ApiFactory::server()->type($this->type)->httpRequest()->setPermission()->get($paramsPlinct);

    //
    $newData = [];

    if ($dataPlinct) {
      foreach ($dataPlinct as $item) {
        $subClass = $item['additionalType'];

        if ($subClass && $subClass !== "") {
          foreach ($dataSoloine['@graph'] as $key => $soloItem) {
            if ($key == $subClass) {
              // set name
              $explode = explode(' > ', $soloItem);
              $newData[] = $explode[1] ?? $soloItem;
            }
          }
        }
      }

      return [
        'format' => 'ClassHierarchy',
        'type' => $this->type,
        'class' => $this->subClass,
        'subClass' => array_values(array_unique($newData))
      ];
    }

    return null;
  }

  /**
   * @return array[]
   */
  public function ready(): array {
    return $this->getSections();
  }
}
