<?php
namespace Plinct\Api\Response\Format\Geojson;

class Geojson
{
	/**
	 * @var array
	 */
  private array $data;
	/**
	 * @var array
	 */
  private array $response = [];

	/**
	 * @param $data
	 */
  public function __construct($data) {
    if (isset($data['error']) || (isset($data['status']) && ($data['status'] == 'error' || $data['status'] == 'fail'))) {
      $this->response['status'] = $data['status'] ?? 'error';
      $this->response['data'] = $data;
    } else {
      $this->response['status'] = "success";
    }
    $this->data = $data;
  }

	/**
	 * @return void
	 */
  private function buildResponse(): void
  {
    $features = [];
    $longitudeData = null;
    $latitudeData = null;
    foreach ($this->data as $item) {
			$longitude = isset($item['longitude']) ? (float) $item['longitude'] : null;
			$latitude = isset($item['latitude']) ? (float) $item['latitude'] : null;
      $longitudeData[] = $longitude;
      $latitudeData[] = $latitude;
      $features[] = [
        'type'=>'Feature',
        'geometry' => [
          'type' => 'Point',
          'coordinates' => [ $longitude, $latitude]
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

    $bbox = [min($longitudeData),min($latitudeData), min($longitudeData),max($latitudeData), max($longitudeData),max($latitudeData), max($longitudeData), min($latitudeData)];

    $this->response = [
        "type"=>'FeatureCollection',
        'bbox'=> $bbox,
        'features'=> $features
    ];
  }

	/**
	 * @return array
	 */
  public function ready(): array
  {
    $this->buildResponse();
    return $this->response;
  }
}
