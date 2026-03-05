<?php
namespace Plinct\Api\Domain\DesignPatterns;

class DesignPatternFactory
{
	// DIRECTORIES
	public const string UPLOAD_PATH = 'public/uploads/';
	public const string IMAGE_PATH = 'public/uploads/image/';
	public const string AUDIO_PATH = 'public/uploads/audio/';
	public const string VIDEO_PATH = 'public/uploads/video/';
	public const string DOCUMENT_PATH = 'public/uploads/document/';
	public const string THUMBNAIL_FORMAT = 'webp';

	// IMAGES AND VIEWS SIZE
	public const int IMAGE_TINY = 320;
	public const int IMAGE_SMALL = 520;
	public const int IMAGE_MEDIUM = 720;
	public const int IMAGE_LARGE = 1280;

	public static function imageMeasures(): array
	{
		return [
			'tiny'   => ['suffix' => '_t', 'width' => 320],
			'small'  => ['suffix' => '_s', 'width' => 520], // ~ 320 * 1,618 = 517,76
			'medium' => ['suffix' => '_m', 'width' => 840], // ~ 520 * 1,618 = 841,36
			'large'  => ['suffix' => null, 'width' => 1290], // 840 * 1,618 = 1360
		];
	}

	public static function fileNameGenerator(string $originalName, string $suffix = null): string
	{
		$date = date('Ymd-His');
		$hash = sha1($originalName);
		return "{$date}_$hash" . ($suffix ?: '');
	}
}
