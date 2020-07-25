<?php

namespace Plinct\Api\Server;

use Plinct\Api\Server\PDOConnect;

class Crud 
{
    protected $table;
    
    
    // READ
    protected function read(string $field = "*", string $where = null, string $groupBy = null, string $orderBy = null, $limit = null, $offset = null, array $args = null) 
    {        
        $query = "SELECT $field FROM $this->table";
        $query .= $where ? " WHERE $where" : null;
        $query .= $groupBy ? " GROUP BY $groupBy" : null;
        $query .= $orderBy ? " ORDER BY $orderBy" : null;
        $query .= $limit ? " LIMIT $limit" : null;
        $query .= $offset ? " OFFSET $offset" : null;
        $query .= ";";
        
        return $this->getQuery($query, $args);
    }
    
    // CREATED
    protected function created(array $data) 
    {   
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
    
    // CREATE RELATIONSHIP
    /*protected function createdRelationship($tableOwner, $idOwner, $tableIsPartOf, $idIsPartOf, $data = null) 
    {        
        $this->table = $tableOwner.'_has_'.$tableIsPartOf;
        
        $idOwnerName = 'id'.$tableOwner;
        $idIsPartOfName = 'id'.$tableIsPartOf;
        
        $dataIds = [ $idOwnerName => $idOwner, $idIsPartOfName => $idIsPartOf ];
        
        $dataFinal = $data ? array_merge($dataIds, $data) : $dataIds;
        
        return $this->created($dataFinal);
    }*/
    

    // UPDATE
    protected function update(array $data, string $where) 
    {   
        if (empty($data)) {
            return [ "message" => "No data from update in CRUD" ];
        }
        // query
        foreach ($data as $key => $value) {
            $names[] = "`$key`=?";
            $bindValues[] = $value;
        }
        
        $query = "UPDATE $this->table SET ";
        $query .= implode(",", $names);
        $query .= " WHERE $where;";
        
        return self::execute($query, $bindValues, "Updated data successfully", $data);
    }
    
    /*protected function updateRelationship($tableOwner, $idOwner, $tableIsPartOf, $idIsPartOf, $data = null)
    {        
        $this->table = $tableOwner.'_has_'.$tableIsPartOf;
        
        $idOwnerName = 'id'.$tableOwner;
        $idIsPartOfName = 'id'.$tableIsPartOf;
        
        $where = "`$idOwnerName`=$idOwner AND `$idIsPartOfName`=$idIsPartOf";
        
        return $this->update($data, $where);
    }*/
    
    // DELETE
    protected function erase(string $where, $limit = null): array 
    {    
        $query = "DELETE FROM $this->table";
        $query .= " WHERE $where";
        $query .= $limit ? " LIMIT $limit" : null;
        $query .= ";";
        
        $run = PDOConnect::run($query);
                
        if (empty($run)) {
            return [ "message" => "Deleted successfully" ];
            
        } else {
            return [ "message" => "unsuccess delete" ];
        }
    }
    
    protected function eraseRelationship($tableOwner, $idOwner, $tableIsPartOf, $idIsPartOf)
    {            
        $this->table = $tableOwner.'_has_'.$tableIsPartOf;
        
        $idOwnerName = 'id'.$tableOwner;
        $idIsPartOfName = 'id'.$tableIsPartOf;
        
        $where = "`$idOwnerName`=$idOwner AND `$idIsPartOfName`=$idIsPartOf";
        
        return $this->erase($where);
    }
        
    private static function execute($query, $bindValues, $message, $data)
    {
        $connect = PDOConnect::getPDOConnect();
        
        // prepare
        $stmt = $connect->prepare($query);        
        
        foreach ($bindValues as $key => $valueBind) {
            $stmt->bindValue(($key+1), $valueBind);
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

        return [ 
            "message" => $message,
            "data" => $data
        ];
    }
    
    // LAST INSERT ID
    protected function lastInsertId(): string 
    {
        $query = "SELECT LAST_INSERT_ID() AS id;";
        $return = $this->getQuery($query);        
        
        return $return[0]['id'];
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
                    "code" => $errorInfo[1],
                    "query" => $query
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
