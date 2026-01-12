<?php

declare(strict_types=1);

namespace Plinct\Api\Response\Format;

use Plinct\Api\Response\Format\ClassHierarchy\ClassHierarchy;
use Plinct\Api\Response\Format\Geojson\Geojson;

class Format
{
  /**
   * @param string $type
   * @param array $params
   * @return ClassHierarchy
   */
  public function classHierarchy(string $type, array $params): ClassHierarchy {
    return new ClassHierarchy($type, $params);
  }

	/**
	 * @param array $data
	 * @return Geojson
	 */
  public function geojson(array $data): Geojson {
    return new Geojson($data);
  }
}