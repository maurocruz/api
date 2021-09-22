<?php

declare(strict_types=1);

namespace Plinct\Api\Server\Search;

use Plinct\PDO\PDOConnect;

class Search extends SearchAbstract
{
    /**
     */
    public function getData($params): array
    {
        $this->setClause($params);

        $this->setProperties($params['properties'] ?? null);

        $this->setSource($params['source'] ?? null);

        $this->setInputValue($params['q'] ?? null);

        $this->setTarget($params['target'] ?? 'name');

        $this->setOrderby($params['orderby'] ?? null);

        $this->setLimit(isset($params['limit']) ? (int)$params['limit'] : null);

        $this->setOffset(isset($params['offset']) ? (int)$params['offset'] : null);

        $this->setFormat($params['format'] ?? null);

        return $this->ready();
    }



    /**
     * @return array
     */
    protected function ready(): array
    {
        // GET LIST ELEMENTS
        $this->itemListElement();

        // GET COUNTS
        if ($this->format == 'ItemList') {
            $this->listItem();
        }
        return $this->data;
    }
}