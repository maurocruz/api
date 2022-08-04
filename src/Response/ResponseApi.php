<?php

declare(strict_types=1);

namespace Plinct\Api\Response;

use Plinct\Api\Response\Message\Message;
use Psr\Http\Message\ResponseInterface;

class ResponseApi
{
	/**
	 * @return Message
	 */
	public static function message(): Message {
		return new Message();
	}

	/**
	 * @param ResponseInterface $response
	 * @param array $data
	 * @return ResponseInterface
	 */
	public static function write(ResponseInterface $response, array $data = ['status'=>'success','message'=>'empty response']): ResponseInterface
	{
		$response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));
		return $response;
	}
}
