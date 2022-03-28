<?php

declare(strict_types=1);

namespace Plinct\Api\Server\Format\Geojson;

class MapboxLayout
{
    public static function getIconImage(string $category = null): string
    {
        switch ($category) {
            case 'BookStore': return 'library';
            case 'City': return 'marker';
            case 'EducationalOrganization': return 'library';
            case 'FoodEstablishment': return 'restaurant';
            case 'LodgingBusiness': return 'lodging';
            case 'Restaurant': return 'restaurant';
            case 'TouristAttraction': return 'marker';
            default: return "marker";
        }
    }
}