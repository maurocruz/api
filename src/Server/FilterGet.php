<?php
namespace Plinct\Api\Server;

class FilterGet extends sqlBuilderAbstract {
    // conditions sql
    private $fields = "*";
    private $groupBy = null;
    private $orderBy = null;
    private $limit = null;
    private $offset = null;
    private $properties;
            
    public function __construct($queryParams, $table, $properties = []) {
        $this->table = $table;
        $this->properties = $properties;
        if (!empty($queryParams)) {
            $this->setQueries($queryParams);
        }
    }
    
    private function setQueries($queryParams) {
        // fields
        $fields = $queryParams['fields'] ?? null;
        $this->fields = $fields ?? $this->fields;
        // query params
        foreach ($queryParams as $key => $value) {
            $idname = "id".$this->table;
            if ($value == "id") {
                $value = $idname;
            }
            // ORDER BY
            if (stripos($key, "orderBy") !== false) {
                $ordering = $queryParams['ordering'] ?? null;
                $this->orderBy = stripos($ordering, 'rand') !== false ? "rand()" : $value." ". $ordering;
            }
            // WHERE
            parent::setWhere($key,$value);
        }
        // groupBy
        $this->groupBy = $queryParams['groupBy'] ?? null;
        // limit
        $this->limit = isset($queryParams['limit']) ? ($queryParams['limit'] !== "none" ? $queryParams['limit'] : null) : $this->limit;         
        // offset        
        $this->offset = $queryParams['offset'] ?? null;
    }
    
    public function where(): ?string {
        return $this->where;
    }
    
    public function orderBy(): ?string {
        return $this->orderBy;
    }
    
    public function limit(): ?int {
        return $this->limit;
    }

    public function offset(): ?string {
        return $this->offset;
    }
    
    public function getProperties(): array {
        return $this->properties;
    }

    public function getSqlStatement(): string {
        return "SELECT $this->fields FROM `$this->table`" . $this->stmtWhere() . $this->stmtGroupBy() . $this->stmtOrderBy(). $this->stmtLimit() . $this->stmtOffset();
    }

    private function stmtWhere(): ?string {
        return $this->where ? " WHERE $this->where" : null;
    }

    private function stmtGroupBy(): ?string {
        return $this->groupBy ? " GROUP BY $this->groupBy" : null;
    }

    private function stmtOrderBy(): ?string {
        return $this->orderBy ? " ORDER BY $this->orderBy" : null;
    }

    private function stmtLimit(): ?string {
        return $this->limit ? " LIMIT $this->limit" : null;
    }

    private function stmtOffset(): ?string {
        return $this->offset !== null ? " OFFSET $this->offset" : null;
    }
}
