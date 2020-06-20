<?php
namespace Fwc\Api\Type;
/**
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
interface ThingGetInterface 
{
    // index, show, create, update, delete
    public function index(string $where = null, $order = null, $limit = null, $offset = null): string;
    
    
            
            
    function listAll(string $where = null, $order = null, $limit = null, $offset = null);
    
    function selectById($id, $order = null, $field = '*');
}
