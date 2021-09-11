<?php

declare(strict_types=1);

namespace Plinct\Api\Server\GetData;

use Plinct\PDO\PDOConnect;

class GetDataAbstract
{
    /**
     * @var
     */
    protected $query;
    /**
     * @var string
     */
    protected $fields = '*';
    /**
     * @var
     */
    protected $table;
    /**
     * @var array
     */
    protected $params = [];

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
        foreach ($this->params as $key => $value) {

            switch ($key) {
                case 'offset':
                    $this->query .= " OFFSET $value";
                    unset($this->params['offset']);
                    break;
                case 'groupBy':
                    $this->query .= " GROUP BY $value";
                    unset($this->params['groupBy']);
                    break;
                case 'orderBy':
                    $ordering = $this->params['ordering'] ?? null;
                    $this->query .= " ORDER BY $value $ordering";
                    unset($this->params['orderBy']);
                    break;
                case 'limit':
                    $this->query .= $value != 'none' ? " LIMIT $value" : null;
                    unset($this->params['limit']);
                    break;
            }
        }
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
        foreach ($columnsTable as $value) {
            $field = $value['Field'];
            if (isset($this->params[$field])) {
                $fieldValue = addslashes($this->params[$field]);
                $where[] = "`$field`='$fieldValue'";
            }
        }

        $this->query .= $where ? " WHERE ". implode(" AND ", $where) : null;
    }
}
