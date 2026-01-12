<?php
namespace Plinct\Api\Helper;

use Exception;
use getID3;

class ParserMidia
{
	private ?string $author = null;
	private ?string $bitrate = null;
	private ?int $contentSize;
	private ?string $dateModified = null;
	private ?string $datePublished = null;
	private ?string $duration = null;
	private ?string $headLine = null;
	private ?string $height = null;
	private ?string $size = null;
	private ?string $width = null;
	private ?string $publisher = null;

	/**
	 * @var string
	 */
	private string $encodingFormat;

	private array $parser;

	/**
	 * @throws Exception
	 */
	public function __construct(string $filename, string $type, int $size)
	{
		$this->contentSize = $size;
		$this->encodingFormat = $type;
		$getID3 = new getID3();
		$this->parser = $getID3->analyze($filename);
		$this->contentSize = $this->parser['filesize'] ?? $size;
		$this->encodingFormat = $this->parser['mime_type'] ?? $type;
		if (!$this->parser) {
			throw new Exception('File not found');
		}
		if ($this->encodingFormat === 'application/pdf') {
			$pdf = $this->parser['pdf'];
			$this->size = isset($pdf['pages']) ? $pdf['pages'].'p' : null;
		}
	}

	/**
	 * @return ?string
	 */
	public function getAuthor(): ?string
	{
		return $this->author;
	}

	/**
	 * @return ?string
	 */
	public function getBitrate(): ?string
	{
		return $this->bitrate;
	}

	/**
	 * @return int|null
	 */
	public function getContentSize(): ?int
	{
		return $this->contentSize;
	}

	/**
	 * @return ?string
	 */
	public function getDateModified(): ?string
	{
		return $this->dateModified;
	}

	/**
	 * @return ?string
	 */
	public function getDatePublished(): ?string
	{
		return $this->datePublished;
	}

	/**
	 * @return ?string
	 */
	public function getDuration(): ?string
	{
		return $this->duration;
	}

	/**
	 * @return ?string
	 */
	public function getEncodingFormat(): ?string
	{
		return $this->encodingFormat;
	}

	/**
	 * @return ?string
	 */
	public function getHeadLine(): ?string
	{
		return $this->headLine;
	}

	/**
	 * @return ?string
	 */
	public function getHeight(): ?string
	{
		return $this->height;
	}

	/**
	 * @return ?string
	 */
	public function getSize(): ?string
	{
		return $this->size;
	}

	/**
	 * @return array
	 */
	public function getParser(): array
	{
		return $this->parser;
	}

	/**
	 * @return ?string
	 */
	public function getPublisher(): ?string
	{
		return $this->publisher;
	}

	/**
	 * @return ?string
	 */
	public function getWidth(): ?string
	{
		return $this->width;
	}
}
