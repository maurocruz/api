<?php

declare(strict_types=1);

namespace Plinct\Api\Server\Format\ClassHierarchy;

use Plinct\Api\PlinctApiFactory;
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
    public function __construct(string $type, array $params)
    {
        $this->type = $type;
        $this->subClass = $params['subClass'] ?? $params['class'] ?? $type;
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
        $dataPlinct = PlinctApiFactory::request($this->type)->get($paramsPlinct)->ready();

        //
        $newData = null;

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

            return [ "data" => [
                'format' => 'ClassHierarchy',
                'type' => $this->type,
                'class' => $this->subClass,
                'subClass' => array_values(array_unique($newData))
            ]];
        }

        return null;
    }

    /**
     * @return array[]
     */
    public function ready(): array
    {
        return $this->getSections();
    }
}