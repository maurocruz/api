<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Place extends Entity
{
    /**
     * @var string
     */
    protected string $table = "place";
    /**
     * @var string
     */
    protected string $type = "Place";
    /**
     * @var array|string[]
     */
    protected array $properties = ["thing"];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = ["thing"=>"Thing","address" => "PostalAddress", "image" => "ImageObject" ];

	/**
	 * @param array|null $params
	 * @return string[]
	 */
	public function post(array $params = null): array
	{
		$idthing = $params['thing'] ?? null;
		if (!$idthing) {
			$params['type'] = 'place';
			$dataThing = ApiFactory::request()->type('thing')->post($params)->ready();
			if (isset($dataThing['error'])) {
				return ApiFactory::response()->message()->error()->anErrorHasOcurred($dataThing);
			} else {
				$idthing = $dataThing['id'];
			}
		}
		return parent::post(['thing' => $idthing]);
	}
}
