<?php

declare(strict_types=1);

namespace Plinct\Api\Response\Format\Geojson;

class MapboxLayout
{
    public static function getIconImage(string $category = null): string
    {
        switch ($category) {
	        case 'EducationalOrganization':
	        case 'BookStore':
						return 'library';
	        case 'TouristAttraction':
	        case 'City':
						return 'marker';
	        case 'Restaurant':
	        case 'FoodEstablishment':
						return 'restaurant';
          case 'LodgingBusiness':
						return 'lodging';
	        default: return "marker";
        }
    }
}