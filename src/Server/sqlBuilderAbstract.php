<?php
namespace Plinct\Api\Server;

abstract class sqlBuilderAbstract {
    protected $table;
    // properties not exists
    protected $noWhere = [ "orderBy", "ordering", "limit", "groupBy", "offset", "id", "properties", "where", "format", "count", "fields", "tableHasPart", "idHasPart" ];
    protected $where;

    protected function setWhere($key, $value) {
        $idname = "id".$this->table;
        $response = null;
        $responseLike = null;
        // if like or search
        $like = stristr($key,"like", true) !== false ? stristr($key,"like", true) : ($key == "q" || $key == "search" ? "name" : null);
        if ($like) {
            foreach (explode(',',$value) as $valueLike) {
                $responseLike[] = "`$like` LIKE '%$valueLike%'";
            }
            $response = "(".implode(" OR ", $responseLike).")";

        } elseif (!in_array($key, $this->noWhere)) {
            if (strpos($value, "|") !== false) {
                $response = "(`$key`='".str_replace("|","' OR `$key`='",addslashes($value))."')";
            } else {
                $response = "`$key`='".addslashes($value)."'";
            }
        }
        if ($key == "id") {
            $response = "`$idname`=$value";
        }
        if (stripos($key, "where") !== false) {
            $response = "$value";
        }
        // SET WHERE
        if ($this->where && $response) {
            $this->where = $this->where . " AND " . $response;
        } elseif ($response) {
            $this->where = $response;
        }
    }
}