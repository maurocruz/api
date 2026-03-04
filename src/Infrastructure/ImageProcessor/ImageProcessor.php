<?php
namespace Plinct\Api\Infrastructure\ImageProcessor;

use Imagick;
use ImagickException;
use Plinct\Api\Domain\Configuration\ConfigurationDomain;
use Plinct\Api\Domain\DesignPatterns\DesignPatternFactory;
use Slim\Psr7\UploadedFile;

class ImageProcessor
{
	public function uploadImage()
	{

	}

	/**
	 * @throws ImagickException
	 */
	public function generateThumbnail(UploadedFile $file, string $outputDir = DesignPatternFactory::UPLOAD_PATH)
	{
		$returns = [];

		// IF DOCUMENT PDF
		if ($file->getClientMediaType() === 'application/pdf') {
			$outputFilePath = $outputDir . DesignPatternFactory::fileNameGenerator($file->getClientFilename(), 'thumb') . "." . DesignPatternFactory::THUMBNAIL_FORMAT;

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


		} else {
			foreach (DesignPatternFactory::imageMeasures() as $imageFormat => $imageQuality) {
				$outputFilename = DesignPatternFactory::fileNameGenerator($file->getClientFilename(), $imageQuality['suffix']);
				$outputFilePath = $outputDir . $outputFilename . '.' . DesignPatternFactory::THUMBNAIL_FORMAT;
				$width = $imageQuality['width'];

				// IMAGICK
				$imagick = new Imagick();
				$imagick->setResolution(200, 200);
				$imagick->readImage($file->getFilePath());

				$imagick->setImageFormat(DesignPatternFactory::THUMBNAIL_FORMAT);
				$imagick->setImageCompressionQuality(85);

				$imagick->thumbnailImage($width, 0);
				$imagick->writeImage($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $outputFilePath);
				$imagick->clear();

				// RETURNS
				$returns[] = [
					'image' => ConfigurationDomain::getHost() . DIRECTORY_SEPARATOR . $outputFilePath,
				];
			}
		}
		return $returns;
	}
}
