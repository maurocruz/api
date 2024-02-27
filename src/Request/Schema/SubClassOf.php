<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Schema;

class SubClassOf
{
	private string $subClassOf;

	private array $graph;

	private array $newGraph = [];

	public function __construct(string $subClassOf, array $graph)
	{
		$this->subClassOf = $subClassOf;
		$this->graph = $graph;
	}

	/**
	 * @return array
	 */
	public function getGraph(): array
	{
		return $this->newGraph;
	}

	public function  isSubClass()
	{
		foreach ($this->graph as $class) {
			if (isset($class['rdfs:subClassOf'])) {
				foreach ($class['rdfs:subClassOf'] as $valueId) {
					if (is_string($valueId) && $this->subClassOf == $valueId) {
						$this->newGraph[] = $class;
					} elseif(is_array($valueId)) {
						foreach ($valueId as $valueInArray) {
							if ($valueInArray === $this->subClassOf) {
								$this->newGraph[] = $class;
							}
						}
					}
				}
			}
		}
	}
}
