<?php
namespace Plinct\Api\Http\Response;

use Plinct\Api\Http\Response\ParseSchema\ParseSchema;

class ResponseFactory
{
	public static function parseSchema(array $data): ?array
	{
		if (isset($data[0])) {
			$result = [];
			foreach ($data as $value) {
				$result[] = (new ParseSchema($value))->ready();
			}
			return $result;
		}
		return (new ParseSchema($data))->ready();
	}
}
