<?php
declare(strict_types=1);
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

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
