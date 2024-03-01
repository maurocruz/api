<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\MediaObject;

use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\HttpRequestInterface;

class MediaObject extends Entity implements HttpRequestInterface
{
	public function __construct()
	{
		$this->setTable('mediaObject');
	}
}
