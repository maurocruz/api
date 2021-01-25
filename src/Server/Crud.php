<?php

namespace Plinct\Api\Server;

use PDOException;

class Crud
{
    protected $table;

    // READ
    protected function read(string $field = "*", string $where = null, string $groupBy = null, string $orderBy = null, $limit = null, $offset = null, array $args = null): array
    {        
        $query = "SELECT $field FROM $this->table";
        $query .= $where ? " WHERE $where" : null;
        $query .= $groupBy ? " GROUP BY $groupBy" : null;
        $query .= $orderBy ? " ORDER BY $orderBy" : null;
        $query .= $limit ? " LIMIT $limit" : null;
        $query .= $offset ? " OFFSET $offset" : null;
        $query .= ";";

        return PDOConnect::run($query, $args);
    }
    
    // CREATED
    protected function created(array $data): array
    {
        $names = null;
        $values = null;
        $bindValues = null;

        if (empty($data)) {
            return [ "message" => "Record in $this->table not created because data is empty" ];
        }
        // query
        foreach ($data as $key => $value) {
            $names[] = "`$key`";
            $values[] = "?";
            $bindValues[] = $value;
        }   
        
        $columns = implode(",", $names);        
        $rows = implode(",", $values);
        $query = "INSERT INTO $this->table ($columns) VALUES ($rows)";
        
        return self::execute($query, $bindValues, "Record in $this->table created successfully", $data);
    }    

    // UPDATE
    protected function update(array $data, string $where): array
    {
        $names = null;
        $bindValues = null;

        if (empty($data)) {
            return [ "message" => "No data from update in CRUD" ];
        }
        // query
        foreach ($data as $key => $value) {
            $names[] = "`$key`=?";
            $bindValues[] = $value;
        }
        
        $query = "UPDATE " . $this->table . " SET ";
        $query .= implode(",", $names);
        $query .= " WHERE $where;";

        return self::execute($query, $bindValues, "Updated data successfully", $data);
    }
    
    // DELETE
    protected function erase(string $where, $limit = null): array 
    {    
        $query = "DELETE FROM " . $this->table . " WHERE $where";
        $query .= $limit ? " LIMIT $limit" : null;
        $query .= ";";
        
        $run = PDOConnect::run($query);
                
        if (empty($run)) {
            return [ "message" => "Deleted successfully" ];
            
        } else {
            return [ "message" => "unsuccess delete" ];
        }
    }
        
    private static function execute($query, $bindValues, $message, $data): array
    {
        $connect = PDOConnect::getPDOConnect();
        
        // prepare
        $stmt = $connect->prepare($query);        
        
        foreach ($bindValues as $key => $valueBind) {
            $stmt->bindValue(($key+1), $valueBind);
        }
        
        try {            
            if ($stmt->execute() === false) {
                throw new PDOException();
            }
            
        } catch (PDOException $exc) {
            return [ "error" => [
                "code" => $stmt->errorCode(),
                "driverCodeError" => $stmt->errorInfo()[1],
                "message" => $stmt->errorInfo()[2]
            ]];
        }

        return [ 
            "message" => $message,
            "data" => $data
        ];
    }
    
    // LAST INSERT ID
    protected function lastInsertId(): string 
    {
        return PDOConnect::lastInsertId();
    }
    
    // 
    protected function getQuery($query, $args = null): array
    {
        return PDOConnect::run($query, $args);
    }
}
