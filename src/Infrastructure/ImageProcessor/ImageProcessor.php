<?php
namespace Plinct\Api\Infrastructure\ImageProcessor;

use Exception;
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

	/**
	 */
	public function getImageMetadata(string $path): array
	{
		$metadata = [];
		try {
			$imagick = new Imagick($path);
			// Obtém todas as propriedades, incluindo prefixos exif: e iptc:
			$properties = $imagick->getImageProperties();
			// Mapeamento para propriedades do Schema.org
			$metadata['width'] = $imagick->getImageWidth();
			$metadata['height'] = $imagick->getImageHeight();
			$metadata['author'] = $properties['exif:Artist'] ?? $properties['iptc:By-line'] ?? null;
			$metadata['copyrightHolder'] = $properties['exif:Copyright'] ?? $properties['iptc:CopyrightNotice'] ?? null;
			$metadata['datePublished'] = $properties['exif:DateTime'] ?? $properties['date:create'] ?? null;
			$metadata['description'] = $properties['exif:ImageDescription'] ?? $properties['iptc:Caption'] ?? null;
			$metadata['headline'] = $properties['iptc:Headline'] ?? null;
			$metadata['keywords'] = $properties['iptc:Keywords'] ?? '';
			return $metadata;
		} catch (Exception) {
			return $metadata;
		}
	}

}

