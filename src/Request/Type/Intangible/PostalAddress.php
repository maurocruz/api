<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class PostalAddress extends Entity
{

	public function __construct()
	{
		$this->setTable('postalAddress');
	}

	public function get(array $params = []): array
	{
		return parent::getData($params);
	}

	public function post(array $params = null): array
	{
		$idthing = $params['thing'] ?? null;
		if ($idthing) {
			return parent::post($params);
		}
		return ApiFactory::response()->message()->fail()->inputDataIsMissing($params);
	}
}
