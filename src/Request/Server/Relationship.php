<?php
namespace Plinct\Api\Request\Server;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Relationship
{
	/**
	 * @var string
	 */
	private string $typeHasPart;
	/**
	 * @var string
	 */
	private string $idHasPart;
	/**
	 * @var string
	 */
	private string $typeIsPartOf;
	/**
	 * @var string
	 */
	private string $idIsPartOf;

	/**
	 * @param string $typeHasPart
	 * @param string $idHasPart
	 * @param string $typeIsPartOf
	 * @param string|null $idIsPartOf
	 */
  public function __construct(string $typeHasPart, string $idHasPart, string $typeIsPartOf, string $idIsPartOf = null)
  {
		$this->typeHasPart = $typeHasPart;
		$this->idHasPart = $idHasPart;
		$this->typeIsPartOf = $typeIsPartOf;
		$this->idIsPartOf = $idIsPartOf;
  }

	public function get()
	{

	}

	public function post()
	{
	}

	public function put()
	{

	}
	public function delete(): array
	{
		return PDOConnect::run("DELETE FROM thing_has_thing WHERE `typeHasPart`='$this->typeHasPart' AND `idHasPart`='$this->idHasPart' AND `idIsPartOf`='$this->idIsPartOf' AND `typeIsPartOf`='$this->typeIsPartOf'");
	}
}
