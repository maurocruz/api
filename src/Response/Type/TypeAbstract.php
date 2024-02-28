<?php
declare(strict_types=1);
namespace Plinct\Api\Response\Type;

abstract class TypeAbstract
{
	/**
	 * @var array
	 */
	protected array $contextSchema;
	/**
	 * @var array
	 */
	protected array $thingData;
	/**
	 * @var array
	 */
	protected array $identifier;

	/**
	 * @param string $type
	 * @return void
	 */
	public function setContextSchema(string $type): void
	{
		$this->contextSchema = ['@context'=>'https://schema.org','@type'=>ucfirst($type)];
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @param string $type
	 */
	public function setIdentifier(string $name, string $value, string $type = 'PropertyValue'): void
	{
		$this->identifier[] = ['@type'=>$type,'name'=>$name,'value'=>$value];
	}

	/**
	 * @param array $value
	 */
	public function setThingData(array $value): void
	{
		$this->setIdentifier('idthing', (string) $value['idthing']);
		$this->setIdentifier('dateCreated', (string) $value['dateCreated']);
		$this->setIdentifier('dateModified', (string) $value['dateModified']);
		unset($value['@context']);
		unset($value['@type']);
		unset($value['idthing']);
		unset($value['dateCreated']);
		unset($value['dateModified']);
		$this->thingData = $value;
	}
}
