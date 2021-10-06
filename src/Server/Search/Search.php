<?php

declare(strict_types=1);

namespace Plinct\Api\Server\Search;


class Search extends SearchAbstract
{
    /**
     * @param $params
     * @return array
     */
    public function getData($params): array
    {
        $this->setClause($params);

        if (isset($params['properties'])) $this->setProperties($params['properties']);

        if (isset($params['source'])) $this->setSource($params['source']);

        if (isset($params['q'])) $this->setInputValue($params['q']);

        $this->setTarget($params['target'] ?? 'name');

        $this->setOrderby($params['orderby'] ?? null);

        if (isset($params['limit'])) $this->setLimit((int)$params['limit']);

        if (isset($params['offset'])) $this->setOffset((int)$params['offset']);

        $this->setFormat($params['format'] ?? null);

        return $this->ready();
    }



    /**
     * @return array
     */
    protected function ready(): array
    {
        // GET LIST ELEMENTS
        if ($this->properties) $this->itemListElement();

        // GET COUNTS
        if ($this->format == 'ItemList') {
            $this->listItem();
        }
        return $this->data;
    }
}