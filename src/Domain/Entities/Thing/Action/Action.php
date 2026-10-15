<?php
namespace Plinct\Api\Domain\Entities\Thing\Action;

use Plinct\Api\Domain\Entities\Thing\Thing;

class Action extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	protected int $idaction;
	private int $thing;
	public ?string $actionProcess = null;
	public string $actionStatus;
	public ?int $agent = null;
	public ?string $endTime = null;
	public mixed $object = null;
	public ?int $provider = null;
	public ?string $result = null;
	public string $startTime;
	public ?string $targetCollection = null;

	public function __construct(array $value = ["@type" => "Action"])
	{
		parent::__construct($value);

		$this->actionProcess = $value['actionProcess'] ?? null;
		$this->actionStatus = $value['actionStatus'] ?? null;
		$this->agent = $value['agent'] ?? null;
		$this->endTime = $value['endTime'] ?? null;
		$this->object = $value['object'] ?? null;
		$this->provider = $value['provider'] ?? null;
		$this->result = $value['result'] ?? null;
		$this->startTime = $value['startTime'] ?? null;
		$this->targetCollection = $value['targetCollection'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idaction;
	}
}
