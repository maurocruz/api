<?php
namespace Plinct\Api\Domain\Modules\Thing\Action;

use Plinct\Api\Domain\Modules\Thing\ThingDomain;

class ActionDomain extends ThingDomain
{
	protected int $idaction;
	protected int $thing;
	public ?string $actionProcess = null;
	public string $actionStatus;
	public ?int $agent = null;
	public ?string $endTime = null;
	public ?int $object = null;
	public ?int $provider = null;
	public ?string $result = null;
	public string $startTime;
	public ?string $targetCollection = null;

}
