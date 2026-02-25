<?php
namespace Plinct\Api\Response;

use Plinct\Api\ApiFactory;
use Plinct\Api\Response\Configuration\ConfigurationResponse;
use Plinct\Api\Response\Message\Message;
use Plinct\Api\Response\Format\Format;
use Plinct\Api\Response\Type\Type;
use Psr\Http\Message\ResponseInterface;

class Response
{
	/**
	 * @param array $data
	 * @return ConfigurationResponse
	 */
	public function configuration(array $data = []): ConfigurationResponse
	{
		return new ConfigurationResponse($data);
	}
	/**
	 * @return Format
	 */
	public function format(): Format {
		return new Format();
	}
	/**
	 * @return Message
	 */
	public function message(): Message {
		return new Message();
	}

	/**
	 * @param string $type
	 * @return Type
	 */
	public function type(string $type): Type
	{
		return new Type($type);
	}

	/**
	 * @param ResponseInterface $response
	 * @param array|null $data
	 * @return ResponseInterface
	 */
	public function write(ResponseInterface $response, ?array $data): ResponseInterface
	{
		$data = $data ?? ApiFactory::response()->message()->success('No data found!');
		$response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));
		return $response;
	}
}
