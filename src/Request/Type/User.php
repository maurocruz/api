<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;

class User extends Entity
{
	/**
	 * @var string
	 */
	protected string $table = "user";
	/**
	 * @var string
	 */
	protected string $type = "Person";
}
