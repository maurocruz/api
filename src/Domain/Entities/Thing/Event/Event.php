<?php
namespace Plinct\Api\Domain\Entities\Thing\Event;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Event extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','Place'];

	protected int $idevent;
	protected int $thing;
	public ?int $about = null;
	public ?string $director = null;
	public ?string $endDate = null;
	public ?string $keywords = null;
	public ?int $location = null;
	public ?string $organizer = null;
	public ?string $startDate = null;
	public ?int $subEvent = null;
	public ?int $superEvent = null;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
