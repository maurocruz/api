<?php

declare(strict_types=1);

namespace Plinct\Api\Response\Format\Geojson;

class Geojson
{
    private array $data;

    private array $response = [];

    public function __construct($data) {

        if (isset($data['error']) || (isset($data['status']) && ($data['status'] == 'error' || $data['status'] == 'fail'))) {
            $this->response['status'] = $data['status'] ?? 'error';
            $this->response['data'] = $data;
        } else {
            $this->response['status'] = "success";
        }

        $this->data = $data;
    }

    private function buildResponse()
    {
        $features = [];
        $longitude = null;
        $latitude = null;

        foreach ($this->data as $item) {
            $longitude[] = (float) $item['longitude'];
            $latitude[] = (float) $item['latitude'];
            $features[] = [
                'type'=>'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $item['longitude'], (float) $item['latitude']]
                ],
                'properties'=>[
                    'idplace'=> $item['idplace'],
                    'name'=> $item['name'],
                    'description' => $item['description'],
                    'additionalType' => $item['additionalType'],
                    'icon-image' => MapboxLayout::getIconImage($item['additionalType'])
                ]
            ];
        }

        $bbox = [min($longitude),min($latitude), min($longitude),max($latitude), max($longitude),max($latitude), max($longitude), min($latitude)];

        $this->response = [
            "type"=>'FeatureCollection',
            'bbox'=> $bbox,
            'features'=> $features
        ];
    }


    public function ready(): array
    {
        $this->buildResponse();

        return $this->response;
    }
}