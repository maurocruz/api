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
	 * @param array|null $uploadedFiles
	 * @return string[]
	 */
	public function post(array $params = null, array $uploadedFiles = null): array
	{
		$params['type'] = 'place';
		$dataThing = ApiFactory::request()->type('thing')->post($params, $uploadedFiles)->ready();
		$idthing = $dataThing['id'];
		return parent::post(['thing'=>$idthing]);
	}
}
