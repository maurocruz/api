<?php
namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;

class User extends Entity
{
	public function __construct()
	{
		$this->setTable('user');
	}
}
