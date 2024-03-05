<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\HttpRequestInterface;

class CreativeWork extends Entity implements HttpRequestInterface
{
	public function __construct()
	{
		$this->setTable('creativeWork');
	}

	public function get(array $params = []): array
	{
		$returns = [];
		$data = parent::getData($params);
		if (!empty($data)) {
			foreach ($data as $item) {
				$idthing = $item['thing'];
				$dataThing = ApiFactory::request()->type('thing')->get(['idthing' => $idthing])->ready();
				$returns[] = $item + $dataThing[0];
			}
		}
		return $returns;
	}
}