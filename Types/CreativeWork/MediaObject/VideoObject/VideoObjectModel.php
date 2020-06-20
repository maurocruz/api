<?php

namespace fwc\Thing;

/**
 * VideoObjectModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

class VideoObjectModel extends Crud
{
    public $table = "videos";
    
    public function __construct() {
        parent::__construct();
    }
    
    public function listAll()
    {
        $query = "SELECT * FROM videos ORDER BY POSITION desc";
        return parent::getQuery($query);
    }
        
    public function showVideo($url)
    {
        $query = "SELECT * FROM videos WHERE url = '{$url}'";
        return parent::getQuery($query);
    }
}
