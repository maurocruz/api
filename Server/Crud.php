<?php

namespace Fwc\Api\Server;

use Fwc\Api\Server\PDOConnect;

abstract class Crud 
{
    protected $table;
    
     // CREATED
    protected function created(array $data) 
    {
        $connect = PDOConnect::getPDOConnect();
        
        // query
        foreach ($data as $key => $value) {
            $names[] = "`$key`";
            $values[] = "?";
            $bindValues[] = $value;
        }   
        
        $columns = implode(",", $names);        
        $rows = implode(",", $values);
        $query = "INSERT INTO $this->table ($columns) VALUES ($rows)";
                
        // prepare
        $stmt = $connect->prepare($query);        
        
        foreach ($bindValues as $key => $value2) {
            $stmt->bindValue(($key+1), $value2);
        }
        
        try {            
            if ($stmt->execute() === false) {
                throw new \PDOException();
            }
            
        } catch (\PDOException $exc) {
            return [ "error" => [
                "code" => $stmt->errorCode(),
                "driverCodeError" => $stmt->errorInfo()[1],
                "message" => $stmt->errorInfo()[2]
            ]];
        }

        return [ "message" => "Created successfully" ];
    }
    
    // READ
    protected function read(string $field = "*", string $where = null, string $groupby = null, string $order = null, array $args = null) 
    {
        $query = "SELECT $field FROM $this->table";
        $query .= $where ? " WHERE $where" : NULL;
        $query .= $groupby ? " GROUP BY $groupby" : NULL;
        $query .= $order ? " ORDER BY $order" : NULL;
        $query .= ";";
        return $this->getQuery($query, $args);
    }

    // UPDATE
    public function update(array $data, string $where) 
    {
        $connect = PDOConnect::getPDOConnect();
        
        // query
        foreach ($data as $key => $value) {
            $names[] = "`$key`=?";
            $values[] = $value;
        }
        
        $query = "UPDATE $this->table SET ";
        $query .= implode(",", $names);
        $query .= " WHERE $where;";
        
        // prepare
        $stmt = $connect->prepare($query);
        
        foreach ($values as $key => $value2) {
            $stmt->bindValue(($key+1), $value2);
        }
        
        $stmt->execute();
        
        return $stmt;
    }
    
    // DELETE
    public function delete(array $where, $limit = null): object 
    {
        $connect = PDOConnect::getPDOConnect();
        
        // query
        foreach ($where as $key => $value) {
            $clause[] = "`$key`=?";
            $values[] = $value;
        }
        
        $conditions = implode(" AND ", $clause);
        $query = "DELETE FROM $this->table WHERE $conditions";
        $query .= $limit ? " LIMIT $limit" : null;
        $query .= ";";
        
        // prepare
        $stmt = $connect->prepare($query);
        
        foreach ($values as $key => $value2) {
            $stmt->bindValue(($key+1), $value2);
        }
        
        $stmt->execute();
        
        return $stmt;
    }
    
    // LAST INSERT ID
    protected function lastInsertId(): string 
    {
        $query = "SELECT LAST_INSERT_ID() AS id;";
        $return = $this->getQuery($query);        
        
        return $return[0]['id'];
    }
    
    protected function getHasTable($tableOwner,$idOwner, $order = null) 
    {
        $tableHas = $tableOwner."_has_".$this->table;
        $idTable = "id".$this->table;
        $query = "SELECT * FROM $this->table, $tableHas WHERE {$this->table}.{$idTable}={$tableHas}.{$idTable} AND {$tableHas}.id{$tableOwner}=$idOwner";
        $query .= $order ? " ORDER BY $order" : null;
        $query .= ";";
        
        return $this->getQuery($query);
    }
    
    // 
    protected function getQuery($query, $args = null) 
    {
        $connect = PDOConnect::getPDOConnect();
     
        try {
            if ($connect && !array_key_exists('error', $connect)) {
                $q = $connect->prepare($query);
                $q->setFetchMode(\PDO::FETCH_ASSOC);
                
                $q->execute($args);
                $errorInfo = $q->errorInfo();

                if ($errorInfo[0] == "0000") {        
                    return $q->fetchAll();

                } else {
                    throw new \PDOException();
                }   
            } else {
                throw new \PDOException();
            }
            
        } catch (\PDOException $e) {
            
            if(array_key_exists('error', $connect)) {
                return $connect;
                
            } elseif ($errorInfo !== '0000') {                
                return [ "error" => [ 
                    "message" => $errorInfo[2],
                    "code" => $errorInfo[1]
                ] ];
            } else {
                return [ "error" => [ 
                    "message" => $e->getMessage(),
                    "code" => $e->getCode()
                ] ];
            }
        }
    }
}
