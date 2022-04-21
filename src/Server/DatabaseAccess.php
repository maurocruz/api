<?php

declare(strict_types=1);

namespace Plinct\Api\Server;

class DatabaseAccess extends DatabaseAccessAbstract implements DatabaseAccesseInterface
{
	/**
	 * @var ?string
	 */
	private ?string $method = null;
	/**
	 * @var string
	 */
	private string $tableHasPart;
	/**
	 * @var string
	 */
	private string $idHasPart;

	/**
	 * @param string $table
	 * @return DatabaseAccesseInterface
	 */
	public function setTable(string $table): DatabaseAccesseInterface
	{
		$this->table = $table;
		return $this;
	}

	/**
	 * @param string $method
	 * @return DatabaseAccesseInterface
	 */
	public function setMethodRequest(string $method): DatabaseAccesseInterface
	{
		$this->method = $method;
		return $this;
	}

	/**
	 * @param array|null $params
	 * @return DatabaseAccesseInterface
	 */
	public function setParams(?array $params): DatabaseAccesseInterface
	{
		if (isset($params['tableHasPart'])) {
			$this->tableHasPart = $params['tableHasPart'];
			unset($params['tableHasPart']);
		}
		if (isset($params['idHasPart'])) {
			$this->idHasPart = $params['idHasPart'];
			unset($params['idHasPart']);
		}
		$this->params = $params;
		return $this;
	}

	/**
	 * @return string[]
	 */
	final public function ready(): array
	{
		switch($this->method) {
			case 'get':
				return parent::get();
			case 'post':
				return parent::post();
			case 'put':
				return parent::put();
			case 'delete':
				return parent::delete();
			default:
				return ['status'=>'fail','message'=>'No method setted'];
		}
	}
}
