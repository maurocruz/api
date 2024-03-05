<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\Request\Server\Entity;

class ContactPoint extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('contactPoint');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		return parent::getData($params);
	}
}
