<?php

declare(strict_types=1);

namespace Plinct\Api\Schema;

class SelectClass
{
	private $graph;
	private $includeSubClass;

	public function __construct(array $graph)
	{
		$this->graph = $graph;
	}

	/**
	 * @param mixed $includeSubClass
	 */
	public function includeSubClass($includeSubClass): void
	{
		$this->includeSubClass = $includeSubClass;
	}

	public function selectClass($id)
	{
		$class = null;
		foreach ($this->graph as $value) {
			if ($value['@type'] == 'rdfs:Class' && $value['@id'] == $id) {
				$class = $value;
			}
		}

		if ($this->includeSubClass && $subClass = $this->addSubClass($class)) {
			$class['subClass'] = $subClass;
		}

		return $class;
	}

	/**
	 * @param array|null $class
	 */
	protected function addSubClass(array $class = null): ?array
	{
		$subClass = null;

		foreach ($this->graph as $value) {
			$subClassOf = $value['rdfs:subClassOf'] ?? null;

			if ($subClassOf && isset($class['@id'])) {
				if (isset($subClassOf['@id']) && $subClassOf['@id'] == $class['@id']) {
					$sc = $this->selectClass($value['@id']);
					if ($sc) {
						$subClass[] = $this->selectClass($value['@id']);
					}

				} elseif (count($subClassOf) > 1) {
					foreach ($subClassOf as $item) {
						$itemId = $item['@id'] ?? null;

						if ($itemId && $itemId == $class['@id']) {
							$sc = $this->selectClass($value['@id']);
							if ($sc) {
								$subClass[] = $this->selectClass($value['@id']);
							}
						}
					}
				}
			}
		}
		return $subClass;
	}
}
