<?php

/**
 * TaxonGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

namespace fwc\Thing;

class TaxonGet extends ThingGetAbstract implements ThingGetInterface
{
    public $table = "herbario";
    public $type = "taxon";

    public function index(string $where = null, $order = null, $groupBy = null, $limit = null, $offset = null): string 
    {
        parent::index($where, $order, $groupBy, $limit, $offset);
    }


    public function listAll(string $where = null, $order = null, $limit = null, $offset = null) {
        $data  = parent::listAll($where, $order, $limit, $offset);
        if( empty($data)) {
            return null;
        } else {
            foreach ($data as $value) {
                $value['image'] = json_decode((new ImageObjectGet())->getHasPart("herbario", $value['idherbario']), true);
                $list[] = self::specie($value);
            }
            return json_encode(ItemList::list(count($data), $list));
        }
        
    }
    
    public function selectById($id, $order = null, $field = '*') {
        $data = parent::selectById($id, $order, $field);
        if (empty($data)) {
            return null;
        } else {
            $value = $data[0];                  
            $value['image'] = json_decode((new ImageObjectGet())->getListImagesIdPartOf('herbario', $value['idherbario']), true);   
            return json_encode(self::specie($value));
        }
    }


    public function getAllByGroupFamily() 
    {
        $data = (new TaxonModel())->getAllFamilies();
        
        foreach ($data as $value) {
            $species = (new TaxonModel())->getAllSpeciesByFamily($value['family']);
            foreach ($species as $valueSpecies) {
                $valueSpecies['image'] = json_decode((new ImageObjectGet())->getPrimaryImageOfTableOwner("herbario", $valueSpecies['idherbario']), true);
                $listSpecies[] = self::specie($valueSpecies);
            }
            $itemListElement[] = self::family($value['family'], $listSpecies);
            unset($listSpecies);
        }        
        
        return json_encode(ItemList::list(count($data), $itemListElement));
    }
    
    public function getFamily($family) {
        $data = (new TaxonModel())->getAllSpeciesByFamily($family);
        
        foreach ($data as $valueSpecies) {
            $valueSpecies['image'] = json_decode((new ImageObjectGet())->getPrimaryImageOfTableOwner("herbario", $valueSpecies['idherbario']), true);
            $listSpecies[] = self::specie($valueSpecies);
        }        
        return json_encode(ItemList::list(count($data), $listSpecies));
    }
    
    public function getAllSpeciesbyGenus($family, $genus) {
        $data = (new TaxonModel())->getSpecie($family, $genus);
        
        foreach ($data as $value) {
            $value['image'] = json_decode((new ImageObjectGet())->getPrimaryImageOfTableOwner("herbario", $value['idherbario']), true);
            $listSpecies[] = self::specie($value);
        }        
        return json_encode(ItemList::list(count($data), $listSpecies));
    }


    public function getSpecie($family, $genus, $specie = null) {
        $data = (new TaxonModel())->getSpecie($family, $genus, $specie);
        $value = $data[0];
        
        $value['image'] = json_decode((new ImageObjectGet())->getHasPart('herbario', $value['idherbario']), true);
        
        return json_encode(self::specie($value));
    }


    static private function family($value, $childTaxon = null) {
        return [
            "@context" => "https://schema.org",
            "@type" => "Taxon",
            "name" => $value,
            "taxonRank" => ["family"],
            "childTaxon" => $childTaxon
        ];
    }
    
    static private function specie($value) {
        return [
            "@context" => "https://schema.org",
            "@type" => "Taxon",
            "identifier" => PropertyValueGet::setPropertyValue([ "idherbario" => $value['idherbario'] ]),
            "name" => $value['genero']." ".$value['especie'],
            "description" => $value['descricao'],
            "taxonRank" => "species",
            "image" => $value['image'],
            "alternateName" => $value['nome'],
            "hasDefinedTerm" => [
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
            ]
        ];
    }
}
