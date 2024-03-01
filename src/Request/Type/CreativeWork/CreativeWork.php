<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\HttpRequestInterface;

class CreativeWork extends Entity implements HttpRequestInterface
{
	public function __construct()
	{
		$this->setTable('creativeWork');
	}
}