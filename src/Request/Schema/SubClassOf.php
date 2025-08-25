<?php
namespace Plinct\Api\Request\Schema;

class SubClassOf
{
	/**
	 * @var string
	 */
	private string $subClassOf;
	/**
	 * @var array
	 */
	private array $graph;
	/**
	 * @var array
	 */
	private array $newGraph = [];

	/**
	 * @param string $subClassOf
	 * @param array $graph
	 */
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

	/**
	 * @return void
	 */
	public function  isSubClass(): void
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
