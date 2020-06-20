<?php
namespace fwc\Thing;
/**
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
interface ModelPostInterface {
    
    function add();
    
    function edit(): bool;
    
    function erase();
    
    function createSqlTable($type = null);
   
    function createNewAndReturnLastId($data): string;
    
    /**
     * Try to add a single item in a parent table.
     * The parent table (TableOwner) has a corresponding ID field to the form table.
     * 
     * @return bool
     */
    function insertNewWithPartOf(string $tableOwner, int $idOwner, array $data);
    
    function insertWithHasPart(string $tableOwner, int $idOwner, string $tableHas, int $idHas, array $data = null);
    
    function deleteWithHasPart(string $tableOwner, int $idOwner, string $tableHas, int $idHas): object;    
}
