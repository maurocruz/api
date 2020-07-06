<?php

namespace Fwc\Api\Type;

class Taxon extends TypeAbstract implements TypeInterface
{    
    protected $table = "taxon";
    
    protected $type = "Taxon";
    
    protected $properties = [ "name", "family", "genus", "specie" ];
    
    protected $withTypes = [ "image" => "ImageObject" ];
    
    public function get(array $params): array 
    {
        $schema = parent::get($params);
        
        
        /*    "hasDefinedTerm" => [
                [ "@type" => "DefinedTerm", "name" => "family", "description" => $value['family'] ],
                [ "@type" => "DefinedTerm", "name" => "genus", "description" => $value['genero'] ],
                [ "@type" => "DefinedTerm", "name" => "specie", "description" => $value['especie'] ],
                [ "@type" => "DefinedTerm", "name" => "botanico", "description" => $value['botanico'] ],
                [ "@type" => "DefinedTerm", "name" => "uso", "description" => $value['uso'] ],
                [ "@type" => "DefinedTerm", "name" => "ocorrencia", "description" => $value['ocorrencia'] ],
                [ "@type" => "DefinedTerm", "name" => "floracao", "description" => $value['floracao'] ],
                [ "@type" => "DefinedTerm", "name" => "frutificacao", "description" => $value['frutificacao'] ],
                [ "@type" => "DefinedTerm", "name" => "porte", "description" => $value['porte'] ],
                [ "@type" => "DefinedTerm", "name" => "raizes", "description" => $value['raizes'] ],
                [ "@type" => "DefinedTerm", "name" => "flores", "description" => $value['flores'] ],
                [ "@type" => "DefinedTerm", "name" => "frutos", "description" => $value['frutos'] ],
                [ "@type" => "DefinedTerm", "name" => "obs", "description" => $value['obs'] ],
                [ "@type" => "DefinedTerm", "name" => "fontes", "description" => $value['fontes'] ],
                [ "@type" => "DefinedTerm", "name" => "gallery", "description" => $value['gallery'] ]
            ]*/
        
        return $schema;
    }
    
    public function post(array $params): array 
    {
        return parent::post($params);
    }
    
    public function put(string $id, $params): array 
    {
        return parent::put($id, $params);
    }
    
    public function delete(string $id, $params): array 
    {
        return parent::delete($id, $params);
    }
    
    public function createSqlTable($type = null) 
    {
        $maintenance = new \Fwc\Api\Server\Maintenance($this->request);
              
        $maintenance->createSqlTable("ImageObject");
        
        return parent::createSqlTable("Taxon");
    } 
}
