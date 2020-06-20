<?php
namespace fwc\Thing;
/**
 * BookGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class BookGet 
{    
    public function getAll($order = "ASC")
    {
        $data = (new BookModel())->getAll($order);
       
        $itens = [];
        foreach ($data as $key => $value) {
            $itens[] = [
                "@type" => "listItem",
                "position" => ($key+1),
                "item" => [
                    "@type" => "Book",
                    "name" => $value['name'],                    
                    "author" => [
                        "@type" => "Person",
                        "name" => $value['author'],
                        "birthDate" => $value['birthDate'],
                        "deathDate" => $value['deathDate']
                    ],
                    "version" => $value['version'],
                    "bookEdition" => $value['bookEdition'],
                    "locationCreated" => $value['locationCreated'],
                    "publisher" => $value['publisher'],
                    "datePublished" => $value['datePublished'],
                    "numberOfPages" => $value['numberOfPages'],
                    "keywords" => $value['keywords']
                ]
            ];
        }
        
        $itemOrder = $order == "ASC" ? "ItemListOrderAscending" : ( $order == "DESC" ? "ItemListOrderDescending" : "ItemListUnordered" );
        
        $response = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "name" => "Bibliografia",
            "numberOfItens" => count($data),
            "itemListOrder" => "http://schema.org/".    $itemOrder,
            "itemListElement" => $itens
        ];
        
        return json_encode($response);
    }
}
