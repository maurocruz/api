<?php
namespace Plinct\Api\Infrastructure\FileProcessor;

use finfo;

class MediaTypeDetector
{
	public const string TYPE_IMAGE = 'image';
	public const string TYPE_VIDEO = 'video';
	public const string TYPE_AUDIO = 'audio';
	public const string TYPE_PDF   = 'pdf';
	public const string TYPE_UNKNOWN = 'unknown';

	public function detect(string $filePath): string
	{
		$signature = $this->detectBySignature($filePath);

		if ($signature !== self::TYPE_UNKNOWN) {
			return $signature;
		}

		$mime = $this->detectByMime($filePath);

		if ($mime) {
			return $this->mapMimeToType($mime);
		}

		return self::TYPE_UNKNOWN;
	}

	private function detectByMime(string $file): ?string
	{
		if (!class_exists('finfo')) {
			return null;
		}

		$finfo = new finfo(FILEINFO_MIME_TYPE);

		return $finfo->file($file);
	}

	private function mapMimeToType(string $mime): string
	{
		if (str_starts_with($mime, 'image/')) {
			return self::TYPE_IMAGE;
		}

		if (str_starts_with($mime, 'video/')) {
			return self::TYPE_VIDEO;
		}

		if (str_starts_with($mime, 'audio/')) {
			return self::TYPE_AUDIO;
		}

		if ($mime === 'application/pdf') {
			return self::TYPE_PDF;
		}

		return self::TYPE_UNKNOWN;
	}

	private function detectBySignature(string $file): string
	{
		$handle = fopen($file, 'rb');

		if (!$handle) {
			return self::TYPE_UNKNOWN;
		}

		$bytes = bin2hex(fread($handle, 16));

		fclose($handle);

		// PDF
		if (str_starts_with($bytes, '25504446')) {
			return self::TYPE_PDF;
		}

		// JPEG
		if (str_starts_with($bytes, 'ffd8ff')) {
			return self::TYPE_IMAGE;
		}

		// PNG
		if (str_starts_with($bytes, '89504e47')) {
			return self::TYPE_IMAGE;
		}

		// GIF
		if (str_starts_with($bytes, '47494638')) {
			return self::TYPE_IMAGE;
		}

		// WEBP
		if (substr($bytes, 16, 8) === '57454250') {
			return self::TYPE_IMAGE;
		}

		// MP3 (ID3)
		if (str_starts_with($bytes, '494433')) {
			return self::TYPE_AUDIO;
		}

		// MP4 / MOV
		if (substr($bytes, 8, 8) === '66747970') {
			return self::TYPE_VIDEO;
		}

		// OGG
		if (str_starts_with($bytes, '4f676753')) {
			return self::TYPE_AUDIO;
		}

		// WAV
		if (str_starts_with($bytes, '52494646')) {
			return self::TYPE_AUDIO;
		}

		return self::TYPE_UNKNOWN;
	}
}
