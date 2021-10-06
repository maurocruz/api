<?php

declare(strict_types=1);

namespace Plinct\Api\Server\ClassHierarchy;

use Plinct\Api\PlinctApiFactory;
use Plinct\Soloine\Factory\SoloineFactory;

class ClassHierarchy implements ClassHierarchyInterface
{
    /**
     * @var string
     */
    private string $class;
    /**
     * @var string
     */
    private string $subClass;

    /**
     * @param string $class
     * @param array $params
     */
    public function __construct(string $class, array $params)
    {
        $this->class = $class;
        $this->subClass = $params['subClass'];
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
        $dataPlinct = PlinctApiFactory::request($this->class)->get($paramsPlinct)->ready();

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
                'type' => $this->class,
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