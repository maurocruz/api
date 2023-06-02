<?php

declare(strict_types=1);

namespace Plinct\Api\Schema;

class Schema extends SchemaAbstract
{
	/**
	 * @param string|null $class
	 */
	public function setClass(string $class = null): void
	{
		$this->class = $class;
	}

	/**
	 * @param string|null $schemas
	 */
	public function setSchemas(string $schemas = null): void
	{
		$this->schemas = $schemas;
	}

	/**
	 * @param string|null $subClass
	 */
	public function setSubClass(?string $subClass = null): void
	{
		$this->subClass = $subClass;
	}

	/**
	 * @param string|null $subClassOf
	 */
	public function setSubClassOf(?string $subClassOf): void
	{
		$this->subClassOf = $subClassOf;
	}

	public final function ready(): array
	{
		parent::mergeSchemas();

		if ($this->class) {
			$select = new SelectClass($this->graph);
			if ($this->subClass) $select->includeSubClass($this->subClass);
			$this->setGraph([$select->selectClass("schema:" . ucfirst($this->class))]);
		}

		if ($this->subClassOf) {
			$subClassOf = new SubClassOf($this->subClassOf, $this->graph);
			$subClassOf->isSubClass();
			$this->setGraph($subClassOf->getGraph());
		}

		return $this->schema;
	}
}
