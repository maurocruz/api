<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Person extends Entity
{
  /**
   * @var string
   */
  protected string $table = "person";
  /**
   * @var string
   */
  protected string $type = "Person";
  /**
   * @var array|string[]
   */
  protected array $properties = [ "thing" ];
  /**
   * @var array|string[]
   */
  protected array $hasTypes = ['thing'=>'Thing',"address" => 'PostalAddress', "contactPoint" => "ContactPoint", "image" => "ImageObject"];

	/**
	 * @param array|null $params
	 * @param array|null $uploadedFiles
	 * @return string[]
	 */
  public function post(array $params = null, array $uploadedFiles = null): array
  {
		$dataThing = ApiFactory::request()->type('thing')->post($params, $uploadedFiles)->ready();
		$idthing = $dataThing['id'];
		return parent::post(['thing'=>$idthing]);

    /*if (isset($params['tableHasPart']) && isset($params['idHasPart']) ) {
      return parent::post($params);
    } else if (isset($params['thing'])) {
			return parent::post($params);
    } else {
      return [ "message" => "incomplete mandatory data" ];
    }*/
  }
}
