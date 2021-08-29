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
        if (isset($this->params['fields'])) {
            $this->setFields();
        }
        // QUERY
        $this->setQuery();

        // WHERE
        $this->parseWhereClause();

        // PARAMS
        if ($this->params) {
            parent::parseParams();
        }

        $this->query .= ";";

        return PDOConnect::run($this->query);
    }
}
