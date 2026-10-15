<?php
namespace Plinct\Api\Domain\Entities\Thing\Event;

use Plinct\Api\Domain\Entities\Thing\Thing;

class Event extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','Place'];

	protected ?int $idevent;
	private ?int $thing;
	public ?int $about = null;
	public ?string $director = null;
	public ?string $endDate = null;
	public ?string $keywords = null;
	public mixed $location = null;
	public ?string $organizer = null;
	public ?string $startDate = null;
	public mixed $subEvent = null;
	public mixed $superEvent = null;

	public function   __construct(array $value = ["@type" => "Event"])
	{
		parent::__construct($value);

		$this->idevent = $value['idevent'] ?? null;
		$this->about = $value['about'] ?? null;
		$this->director = $value['director'] ?? null;
		$this->endDate = $value['endDate'] ?? null;
		$this->keywords = $value['keywords'] ?? null;
		$this->location = $value['location'] ?? null;
		$this->organizer = $value['organizer'] ?? null;
		$this->startDate = $value['startDate'] ?? null;
		$this->subEvent = $value['subEvent'] ?? null;
		$this->superEvent = $value['superEvent'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idevent;
	}
}
