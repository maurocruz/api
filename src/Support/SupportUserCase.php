<?php
namespace Plinct\Api\Support;

use ImagickException;
use Plinct\Tool\Image\ImageProcessor;

class SupportUserCase
{
	public static function propertiesToArray(string $properties = null): ?array
	{
		if (!$properties) return [];
		$propertiesArray = explode(',',$properties);
		array_walk($propertiesArray, function (&$value) {$value = trim($value);});
		return $propertiesArray;
	}

	public static function sortData(array $array): array
	{
		$new_array = array();
		foreach (array_values($array) as $key => $value) {
			ksort($value);
			$new_array[$key] = $value;
		}
		return $new_array;
	}

	/**
	 * @throws ImagickException
	 */
	public static function imageProcessorTool(string $sourcePath, string $outputDir): ImageProcessor
	{
		return new ImageProcessor($sourcePath, $outputDir);
	}
}
