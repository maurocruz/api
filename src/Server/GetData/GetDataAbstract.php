<?php

declare(strict_types=1);

namespace Plinct\Api\Server\GetData;

use Plinct\PDO\PDOConnect;

class GetDataAbstract
{
    /**
     * @var string
     */
    protected string $query;
    /**
     * @var string
     */
    protected string $fields = '*';
    /**
     * @var string
     */
    protected string $table;
    /**
     * @var array
     */
    protected array $params = [];
    /**
     * @var bool|array
     */
    protected $error = false;

    /**
     *
     */
    public function setQuery()
    {
        $this->query = "SELECT $this->fields FROM `$this->table`";
    }

    /**
     * @param mixed $params
     */
    public function setParams($params): void
    {
        $this->params = $params;
    }

    /**
     */
    protected function setFields(): void
    {
        $this->fields = $this->params['fields'];
        unset($this->params['fields']);
    }

    /**
     *
     */
    protected function parseParams()
    {
        $groupBy = $this->params['groupBy'] ?? null;
        $orderBy = $this->params['orderBy'] ?? null;
        $ordering = $this->params['ordering'] ?? null;
        $limit = $this->params['limit'] ?? null;
        $offset = $this->params['offset'] ?? null;

        // GROUP BY
        if ($groupBy) $this->query .= " GROUP BY $groupBy";

        // ORDER BY
        if ($orderBy) $this->query .= " ORDER BY $orderBy $ordering";

        // LIMIT
        if ($limit)  $this->query .= $limit != 'none' ? " LIMIT $limit" : null;

        // OFFSET
        if ($offset) $this->query .= " OFFSET $offset";
    }

    /**
     *
     */
    protected function parseWhereClause()
    {
        $where = null;

        foreach ($this->params as $key => $value) {
            // WHERE
            if ($key == 'where') $where[] = $value;
            // ID
            if ($key == 'id') {
                $idname = "id$this->table";
                $where[] = "`$idname`=$value";
            }
            // LIKE
            $like = stristr($key,"like", true);
            if ($like) {
                $where[] = "LOWER(REPLACE(`$like`,' ','')) LIKE LOWER(REPLACE('%$value%',' ',''))";
            }
        }

        //
        $columnsTable = PDOConnect::run("SHOW COLUMNS FROM `$this->table`;");

        if (isset($columnsTable['error'])) {
            $this->error = $columnsTable;

        } else {
            foreach ($columnsTable as $value) {
                $field = $value['Field'];
                $valueField = $this->params[$field] ?? null;
                if ($valueField) {
                    $fieldValue = is_string($valueField) ? addslashes($valueField) : $valueField;
                    $where[] = "`$field`='$fieldValue'";
                }
            }

            $this->query .= $where ? " WHERE " . implode(" AND ", $where) : null;
        }
    }
}
