<?php

declare(strict_types=1);

namespace Plinct\Api\Server\Search;

use Plinct\PDO\PDOConnect;
use Plinct\Web\Debug\Debug;

abstract class SearchAbstract
{
    protected array $clause;

    protected ?string $properties;

    protected ?array $source;

    protected ?string $inputValue;

    protected string $target = 'name';

    protected ?string $orderby;

    protected int $limit;

    protected int $offset = 0;

    protected ?string $format;

    protected array $data = [];

    protected string $sqlQuery;

    /**
     * @param array $clause
     */
    protected function setClause(array $clause): void
    {
        $this->clause = $clause;
    }

    /**
     * @param string|null $properties
     */
    protected function setProperties(?string $properties): void
    {
        $propertiesArray = [];

        foreach (explode(',',$properties) as $value) {
            $propertiesArray[] = "`".trim($value)."`";
        }

        $this->properties = implode(',',$propertiesArray);

        unset($this->clause['properties']);
    }

    protected function setSource($source): void
    {
        $this->source = explode(',', $source);
        unset($this->clause['source']);
    }

    /**
     * @param string|null $inputValue
     */
    protected function setInputValue(?string $inputValue): void
    {
        $this->inputValue = $inputValue;
        unset($this->clause['q']);
    }

    /**
     * @param string $target
     */
    protected function setTarget(string $target): void
    {
        $this->target = $target;
        unset($this->clause['target']);
    }

    /**
     * @param string|null $orderby
     */
    protected function setOrderby(?string $orderby): void
    {
        $this->orderby = $orderby;
    }

    /**
     * @param int $limit
     */
    protected function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @param int $offset
     */
    protected function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @param string|null $format
     */
    protected function setFormat(?string $format): void
    {
        $this->format = $format;
    }

    protected function itemListElement()
    {
        $sqlQuery = $this->statementSelect($this->properties);

        if ($this->orderby) $sqlQuery .= " ORDER BY $this->orderby";

        if ($this->limit) $sqlQuery .= " LIMIT $this->limit";

        if ($this->offset) $sqlQuery .= " OFFSET $this->offset";

        $sqlQuery .= ";";

        $this->data = PDOConnect::run($sqlQuery);
    }

    /**
     *
     */
    protected function listItem()
    {
        if (!isset($this->data['error'])) {
            $numberOfItems = 0;

            $data = $this->data;
            unset($this->data);

            $dataCount = PDOConnect::run($this->statementSelect("count(*)"));
            foreach ($dataCount as $value) {
                $numberOfItems += $value['count(*)'];
            }

            $this->data['@type'] = "ItemList";
            $this->data['numberOfItems'] = $numberOfItems;
            $this->data['distribuition'] = $dataCount;
            $this->data['limit'] = $this->limit;
            $this->data['offset'] = $this->offset;

            foreach ($data as $key => $itemValue) {
                $item['@type'] = "ListItem";
                $item['position'] = $key + 1;
                $item['item'] = $itemValue;

                $this->data['itemListElement'][] = $item;
            }
        }
    }

    /**
     * @param $fields
     * @return string
     */
    private function statementSelect($fields): string
    {
        $sqlQuery = null;

        foreach ($this->source as $table) {
            $type = ucfirst($table);
            $sqlQuery[] = "(SELECT '$type' as '@type',$fields FROM `$table` WHERE `$this->target` LIKE '%$this->inputValue%')";
        }

        if(count($sqlQuery) > 1) {
            $sqlQuery = implode(" UNION ", $sqlQuery);
        } else {
            $sqlQuery = $sqlQuery[0];
        }

        return $sqlQuery;
    }
}