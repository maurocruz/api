<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\HttpRequestInterface;

class Thing extends Entity implements HttpRequestInterface
{
  /**
   * @var string
   */
  protected string $type = "Thing";
	/**
	 * @var array|string[]
	 */
	protected array $properties = ["contactPoint"];

	public function __construct()
	{
		$this->setTable('thing');
	}
}
