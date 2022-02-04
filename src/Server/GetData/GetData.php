<?php

declare(strict_types=1);

namespace Plinct\Api\Server\GetData;

use Plinct\PDO\PDOConnect;

class GetData extends GetDataAbstract
{
    /**
     * @param $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * @return array
     */
    public function render(): array
    {
        // FIELDS
        $this->setFields();

        // QUERY
        $this->setQuery();

        // WHERE
        $this->parseWhereClause();

        if($this->error) {
            return $this->error;
        }

        // PARAMS
        if ($this->params) {
            parent::parseParams();
        }

        if ($this->limit != 'none' && $this->limit != '') {
            $this->query .= " LIMIT $this->limit";
        }

        $this->query .= ";";

        return PDOConnect::run($this->query);
    }
}
