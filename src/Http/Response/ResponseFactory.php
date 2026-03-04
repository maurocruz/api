<?php
namespace Plinct\Api\Http\Response;

use Plinct\Api\Http\Response\ParseSchema\ParseSchema;

class ResponseFactory
{
	public static function parseSchema(array $data): ?array
	{
		return (new ParseSchema($data))->ready();
	}
}
