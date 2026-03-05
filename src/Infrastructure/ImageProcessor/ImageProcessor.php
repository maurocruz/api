<?php
namespace Plinct\Api\Infrastructure\ImageProcessor;

use Imagick;
use ImagickException;
use Plinct\Api\Domain\Configuration\ConfigurationDomain;
use Plinct\Api\Domain\DesignPatterns\DesignPatternFactory;
use Slim\Psr7\UploadedFile;

class ImageProcessor
{
	

	/**
	 * @throws ImagickException
	 */
	public function generateThumbnailPDF(UploadedFile $file, string $outputDir = DesignPatternFactory::UPLOAD_PATH): array|string
	{

			$outputFilePath = $outputDir . DesignPatternFactory::fileNameGenerator($file->getClientFilename()) . "." . DesignPatternFactory::THUMBNAIL_FORMAT;

			$imagick = new Imagick();
			$imagick->readImage($file->getFilePath() . '[0]');
			// Força fundo branco
			$imagick->setImageBackgroundColor('white');
			// Remove transparência corretamente
			if ($imagick->getImageAlphaChannel()) {
				$imagick = $imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
			} // Garante RGB (remove canal alpha)
			$imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
			$imagick->setImageColorspace(Imagick::COLORSPACE_RGB);
			$imagick->setImageFormat(DesignPatternFactory::THUMBNAIL_FORMAT);
			$imagick->setImageCompressionQuality(85);

			$imagick->thumbnailImage(DesignPatternFactory::IMAGE_MEDIUM, 0);
			$imagick->writeImage($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $outputFilePath);
			$imagick->clear();

			return ConfigurationDomain::getHost() . DIRECTORY_SEPARATOR . $outputFilePath;
	}

	/**
	 * @throws ImagickException
	 */
	public function generateThumbnailImage(UploadedFile $file, string $newFilenamePath = DesignPatternFactory::UPLOAD_PATH): void
	{
		$sourcePath = $file->getFilePath();
		$documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', DIRECTORY_SEPARATOR);
		$format = DesignPatternFactory::THUMBNAIL_FORMAT;
		$compressionQuality = 85;

		$imagick = new Imagick($sourcePath);

		try {
			$sourceWidth = $imagick->getImageWidth();

			$writeThumbnail = static function (Imagick $base, int $targetWidth, string $absoluteOutputPath, string $format, int $quality): void {
				$thumbnail = clone $base;
				try {
					$thumbnail->setImageFormat($format);
					$thumbnail->setImageCompressionQuality($quality);
					$thumbnail->thumbnailImage($targetWidth, 0);
					$thumbnail->writeImage($absoluteOutputPath);
				} finally {
					$thumbnail->clear();
				}
			};

			foreach (DesignPatternFactory::imageMeasures() as $measure) {
				$maxWidth = (int) $measure['width'];
				$targetWidth = min($sourceWidth, $maxWidth);

				$isOriginalSize = ($targetWidth === $sourceWidth);
				$suffix = $isOriginalSize ? '' : (string) ($measure['suffix'] ?? '');

				$outputRelativePath = $newFilenamePath . $suffix . '.' . $format;
				$absoluteOutputPath = $documentRoot . DIRECTORY_SEPARATOR . $outputRelativePath;

				$writeThumbnail($imagick, $targetWidth, $absoluteOutputPath, $format, $compressionQuality);

				if ($isOriginalSize) {
					break;
				}
			}
		} finally {
			$imagick->clear();
		}
	}
}
