<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Service;

use Plinct\Api\Domain\Entities\Thing\Thing;

class Service extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	protected int $idservice;
	protected int $thing;
	public mixed $provider = null;
	public ?string $category = null;
	public ?string $serviceType = null;
	public ?string $termsOfService = null;
	public ?int $isRelatedTo = null;
	public ?int $serviceOutput = null;

}
