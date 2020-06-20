<?php

namespace fwc\Thing;

/**
 * BannerModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

class BannerModel extends Crud 
{
    public $table = "banners";
    
    public function getItensByIdContract($id) 
    {
        $query = "SELECT * FROM $this->table WHERE idadvertising=$id";
        return parent::getQuery($query);
    }
    
    public function getResponsivesBanner($target = null, $position = null) 
    {
        $tags = array_filter(explode("/",$_SERVER['REQUEST_URI']));
        
        $query = "SELECT * FROM banners, advertising";
        $query .= " WHERE banners.status=1 AND advertising.idadvertising=banners.idadvertising AND advertising.status=1 AND advertising.tipo=4";
        $query .= $target ? " AND banners.target='$target'" : null;
        $query .= $position ? " AND banners.position='$position'" : null;
        
        foreach ($tags as $value) {
            $tagsResult[] = " banners.tags LIKE '%$value%' ";
        }
        $query .= isset($tagsResult) ? " AND (". implode(" AND ", $tagsResult) .")" : null;
        
        $query .= " GROUP BY banners.idbanners, advertising.idadvertising";
        $query .= " ORDER BY RAND()";
        $query .= ";";
        //print $query;
        return parent::getQuery($query);
    }
    
}
