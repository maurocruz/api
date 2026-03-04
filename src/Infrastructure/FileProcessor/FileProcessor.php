<?php
namespace Plinct\Api\Infrastructure\FileProcessor;

use Exception;
use getID3;
use Plinct\Api\Domain\Configuration\ConfigurationDomain;
use Plinct\Api\Domain\DesignPatterns\DesignPatternFactory;
use Plinct\Api\Infrastructure\ImageProcessor\ImageProcessor;
use Slim\Psr7\UploadedFile;

class FileProcessor
{
	private string $filePath;
	private string $fileName;
	private string $fileType;
	private string $fileSize;
	private string $contentUrl;
	private string $imageRepresentative;
	private string $directoryDestination;
	private string $fileExtension;

	public function __construct(private readonly ImageProcessor $imageProcessor)
	{
	}

	/**
	 * @return string
	 */
	public function getContentUrl(): string
	{
		return $this->contentUrl;
	}

	/**
	 * @return string
	 */
	public function getDirectoryDestination(): string
	{
		return $this->directoryDestination;
	}

	/**
	 * @return string
	 */
	public function getImageRepresentative(): string
	{
		return $this->imageRepresentative;
	}


	/**
	 * @return string
	 */
	public function getFileName(): string
	{
		return $this->fileName;
	}

	/**
	 * @return string
	 */
	public function getFilePath(): string
	{
		return $this->filePath;
	}

	/**
	 * @return string
	 */
	public function getFileSize(): string
	{
		return $this->fileSize;
	}

	/**
	 * @return string
	 */
	public function getFileType(): string
	{
		return $this->fileType;
	}

	/**
	 * @param UploadedFile $file
	 * @throws Exception
	 */
	public function setUploadFile(UploadedFile $file): void
	{
		if (!is_uploaded_file($file->getFilePath())) {
			throw new Exception('File not uploaded');
		}

		if ($file->getError()!== UPLOAD_ERR_OK) {
			throw new Exception('Erro ao enviar arquivo');
		}
		$this->filePath = $file->getFilePath();
		$this->fileName = $file->getClientFilename();
		$this->fileType = $file->getClientMediaType();
		$this->fileSize = $file->getSize();
		$this->directoryDestination = match ($file->getClientMediaType()) {
			'application/pdf' => DesignPatternFactory::DOCUMENT_PATH,
			'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml' => DesignPatternFactory::IMAGE_PATH,
			'default' => DesignPatternFactory::UPLOAD_PATH
		};
		$this->fileExtension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
	}

	public function getMetadata(): array
	{
		if ($this->fileType === 'application/pdf'){
			$pdfExtractor = new PdfMetadataExtractor($this->filePath);
			return $pdfExtractor->extract();
		}
		return [];
	}

	/**
	 * @throws Exception
	 */
	public function uploadFile(UploadedFile $file): bool
	{
		// VERIFICA ERRO
		if ($file->getError() !== UPLOAD_ERR_OK) {
			throw new Exception('Erro ao enviar arquivo');
		}
		// SE UPLOADED
		if(!is_uploaded_file($file->getFilePath())) {
			throw new Exception('Erro ao enviar arquivo');
		}
		if (!is_dir($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->directoryDestination)){
			mkdir($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->directoryDestination, 0755, true);
		}
		// GENERATE THUMBNAILS
		$this->imageRepresentative = $this->imageProcessor->generateThumbnail($file, $this->directoryDestination);

		// CONTENT URL
		$this->contentUrl = ConfigurationDomain::getHost() . DIRECTORY_SEPARATOR . $this->directoryDestination . DesignPatternFactory::fileNameGenerator($file->getClientFilename()) . "." . $this->fileExtension;

		// MOVE FILE TO UPLOADS
		return move_uploaded_file(
			$file->getFilePath(),
			$_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->directoryDestination . DesignPatternFactory::fileNameGenerator($file->getClientFilename()) . "." . $this->fileExtension
		);
	}

	/**
	 * @throws Exception
	 */
	public function parseMidia(string $filename): array
	{
		$getID3 = new getID3();
		$parser = $getID3->analyze($filename);
		if (!$parser) {
			throw new Exception('File not found');
		}
		return $parser;
	}

}
