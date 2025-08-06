<?php
namespace Plinct\Api\Helper;

use Exception;
use getID3;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Parser;

class ParserMidia
{
	/**
	 * @var string
	 */
	private string $encodingFormat;
	/**
	 * @var array|Document|null
	 */
	private array|null|Document $parser;

	/**
	 * @throws Exception
	 */
	public function __construct(string $filename, string $type)
	{
		$this->encodingFormat = $type;
		if ($type === 'application/pdf') {
			$parser = new Parser();
			$this->parser = $parser->parseFile($filename);
		} else {
			$getID3 = new getID3();
			$this->parser = $getID3->analyze($filename);
		}
	}

	/**
	 * @return mixed|null
	 */
	public function getAuthor(): mixed
	{
		if ($this->encodingFormat === 'application/pdf') {
			return $this->parser->getDetails()['Creator'] ?? $this->parser->getDetails()['dc:creator'] ?? null;
		}
		return null;
	}

	/**
	 * @return mixed|null
	 */
	public function getBitrate(): mixed
	{
		if ($this->encodingFormat !== 'application/pdf') {
			return $this->parser['bitrate'] ?? $this->parser['video']['bitrate'] ?? null;
		}
		return null;
	}

	/**
	 * @return mixed|null
	 */
	public function getDatePublished(): mixed
	{
		if ($this->encodingFormat === 'application/pdf') {
			return $this->parser->getDetails()['CreationDate'] ?? $this->parser->getDetails()['xmp:createdate'] ?? null;
		}
		return null;
	}

	/**
	 * @return mixed|null
	 */
	public function getDateModified(): mixed
	{
		if ($this->encodingFormat === 'application/pdf') {
			return $this->parser->getDetails()['ModDate'] ?? $this->parser->getDetails()['xmp:modifydate'] ?? null;
		}
		return null;
	}

	/**
	 * @throws Exception
	 */
	public function getDetails(): array
	{
		return $this->parser ?? [];
	}

	/**
	 * @return mixed|null
	 */
	public function getDuration(): mixed
	{
		if ($this->encodingFormat !== 'application/pdf') {
			return $this->parser['playtime_string'] ?? $this->parser['video']['duration'] ?? null;
		}
		return null;
	}

	/**
	 * @return string
	 */
	public function getEncodingFormat(): string
	{
		return $this->encodingFormat;
	}

	/**
	 * @return mixed|null
	 */
	public function getHeadLine(): mixed
	{
		if ($this->encodingFormat === 'application/pdf') {
			return $this->parser->getDetails()['Title'] ?? $this->parser->getDetails()['dc:title'] ?? null;
		}
		return null;
	}

	/**
	 * @return mixed|null
	 */
	public function getHeight(): mixed
	{
		if ($this->encodingFormat !== 'application/pdf') {
			return $this->parser['video']['resolution_y'] ?? null;
		}
		return null;
	}

	/**
	 * @return mixed|null
	 */
	public function getPublisher(): mixed
	{
		if ($this->encodingFormat === 'application/pdf') {
			return $this->parser->getDetails()['Producer'] ?? $this->parser->getDetails()['pdf:producer'] ?? null;
		}
		return null;
	}

	/**
	 * @return mixed|null
	 */
	public function getWidth(): mixed
	{
		if ($this->encodingFormat !== 'application/pdf') {
			return $this->parser['video']['resolution_x'] ?? null;
		}
		return null;
	}
}
