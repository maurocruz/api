<?php
namespace Plinct\Api\Http\Response\ParseSchema;

abstract class TypeSchemaAbstract
{
	/**
	 * @var array
	 */
	protected array $contextSchema;
	/**
	 * @var array
	 */
	protected array $thingData = [];
	/**
	 * @var array
	 */
	protected array $identifier = [];
	/**
	 * @var string
	 */
	protected string $type;

	/**
	 * @param string $type
	 * @return void
	 */
	public function setContextSchema(string $type): void
	{
		$this->type = $type;
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
}
