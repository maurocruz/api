<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\Book;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class Book extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	private ?int $thing;
	protected ?int $idbook;
	protected ?int $creativeWork;
	public ?string $bookFormat = null;
	public ?int $illustrator = null;
	public ?string $isbn = null;
	public ?string $bookEdition = null;
	public ?string $numberOfPages = null;

	public function __construct(array $value = ["@type" => "Book"])
	{
		parent::__construct($value);

		$this->idbook = $value['idbook'] ?? null;
		$this->creativeWork = $value['creativeWork'] ?? null;
		$this->bookFormat = $value['bookFormat'] ?? null;
		$this->illustrator = $value['illustrator'] ?? null;
		$this->isbn = $value['isbn'] ?? null;
		$this->bookEdition = $value['bookEdition'] ?? null;
		$this->numberOfPages = $value['numberOfPages'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idbook;
	}
}
