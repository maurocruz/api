<?php
namespace Plinct\Api\Domain\DesignPatterns;

class DesignPatternFactory
{
	// DIRECTORIES
	public const UPLOAD_PATH = 'public/uploads/';
	public const IMAGE_PATH = 'public/uploads/image/';
	public const AUDIO_PATH = 'public/uploads/audio/';
	public const VIDEO_PATH = 'public/uploads/video/';
	public const DOCUMENT_PATH = 'public/uploads/document/';
	public const THUMBNAIL_FORMAT = 'webp';

	// IMAGES AND VIEWS SIZE
	public const IMAGE_TINY = 320;
	public const IMAGE_SMALL = 520;
	public const IMAGE_MEDIUM = 840;
	public const IMAGE_LARGE = 1280;

	/**
	 * @return array[]
	 */
	public static function imageMeasures(): array
	{
		return [
			'tiny'   => ['suffix' => '_t', 'width' => self::IMAGE_TINY],
			'small'  => ['suffix' => '_s', 'width' => self::IMAGE_SMALL], // ~ 320 * 1,618 = 517,76
			'medium' => ['suffix' => '_m', 'width' => self::IMAGE_MEDIUM], // ~ 520 * 1,618 = 841,36
			'large'  => ['suffix' => null, 'width' => self::IMAGE_LARGE], // 840 * 1,618 = 1360
		];
	}

	/**
	 * @param string $originalName
	 * @param string|null $suffix
	 * @return string
	 */
	public static function fileNameGenerator(string $originalName, string $suffix = null): string
	{
		$date = date('Ymd-His');
		$hash = sha1($originalName);
		return "{$date}_$hash" . ($suffix ?: '');
	}
}
