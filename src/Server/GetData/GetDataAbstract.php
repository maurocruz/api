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

        if (isset($this->params['where'])) {
            $where[] = $this->params['where'];
        }

        if (isset($this->params['id'])) {
            $idname = "id$this->table";
            $where[] = "`$idname`={$this->params['id']}";
        }

        if(isset($this->params['nameLike'])) {
            $where[] = "`name` LIKE '%{$this->params['nameLike']}%'";
        }

        if(isset($this->params['headlineLike'])) {
            $where[] = "`headline` LIKE '%{$this->params['headlineLike']}%'";
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
