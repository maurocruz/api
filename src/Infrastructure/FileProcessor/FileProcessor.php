<?php
namespace Plinct\Api\Infrastructure\FileProcessor;

use Exception;
use ImagickException;
use Plinct\Api\Domain\Configuration\ConfigurationDomain;
use Plinct\Api\Domain\DesignPatterns\DesignPatternFactory;
use Plinct\Api\Infrastructure\ImageProcessor\ImageProcessor;
use Slim\Psr7\UploadedFile;

class FileProcessor
{
	private UploadedFile $file;
	private string $filePath;
	private string $fileName;
	private string $fileType;
	private string $fileSize;
	private ?string $contentUrl = null;
	private ?string $imageRepresentative = null;
	private string $directoryDestination;
	private string $fileExtension;
	private string $newFileNamePath;

	public function __construct(private readonly ImageProcessor $imageProcessor)
	{
	}

	/**
	 * @return ?string
	 */
	public function getContentUrl(): ?string
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
	 * @return ?string
	 */
	public function getImageRepresentative(): ?string
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
		$this->file = $file;
		$this->filePath = $file->getFilePath();
		$this->fileName = $file->getClientFilename();
		$this->fileType = $file->getClientMediaType();
		$this->fileSize = $file->getSize();

		$this->directoryDestination = match ($file->getClientMediaType()) {
			'application/pdf' => DesignPatternFactory::DOCUMENT_PATH,
			'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml' => DesignPatternFactory::IMAGE_PATH,
			'default' => DesignPatternFactory::UPLOAD_PATH
		};
		$this->newFileNamePath =  $this->directoryDestination . DesignPatternFactory::fileNameGenerator($file->getClientFilename());
		$this->fileExtension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
	}

	/**
	 * @param string $directoryDestination
	 */
	public function setDirectoryDestination(string $directoryDestination): void
	{
		$this->directoryDestination = $this->directoryDestination . $directoryDestination. DIRECTORY_SEPARATOR;
		$this->newFileNamePath =  $this->directoryDestination . DesignPatternFactory::fileNameGenerator($this->file->getClientFilename());
	}

	/**
	 * @return array
	 */
	public function getMetadata(): array
	{
		if ($this->fileType === 'application/pdf') {
			$pdfExtractor = new PdfMetadataExtractor($this->filePath);
			return $pdfExtractor->extract();
		}

		if (str_starts_with($this->fileType, 'image/')) {
			return $this->imageProcessor->getImageMetadata($this->filePath);
		}
		return [];
	}

	/**
	 * @throws Exception
	 */
	public function uploadFile(): void
	{
		// VERIFICA ERRO
		if ($this->file->getError() !== UPLOAD_ERR_OK) {
			throw new Exception('Erro ao enviar arquivo');
		}
		// SE UPLOADED
		if(!is_uploaded_file($this->file->getFilePath())) {
			throw new Exception('Erro ao enviar arquivo');
		}
		// MAKES DIRECTORY IF NOT EXISTS
		if (!is_dir($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->directoryDestination)){
			mkdir($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->directoryDestination, 0755, true);
		}

		if ($this->file->getClientMediaType() === 'image/svg+xml') {
			$this->uploadSVG();
		}
		// IF IMAGE
		elseif (str_starts_with($this->file->getClientMediaType(), 'image/')) {
			$this->uploadImage();
		}
		// IF DOCUMENT PDF
		elseif ($this->file->getClientMediaType() === 'application/pdf') {
			$this->uploadPDF();
		}
	}

	private function uploadSVG(): void
	{
		// SET CONTENT URL
		$this->imageRepresentative = ConfigurationDomain::getHost() . DIRECTORY_SEPARATOR . $this->newFileNamePath . "." . $this->fileExtension;
		$this->contentUrl = $this->imageRepresentative;
		move_uploaded_file(
			$this->file->getFilePath(),
			$_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->newFileNamePath . "." . $this->fileExtension
		);
	}

	/**
	 * @throws ImagickException
	 */
	private function uploadImage(): void
	{
		// GENERATE THUMBNAILS
		$this->imageProcessor->generateThumbnailImage($this->file, $this->newFileNamePath);
		// SET CONTENT URL
		$this->imageRepresentative = ConfigurationDomain::getHost() . DIRECTORY_SEPARATOR . $this->newFileNamePath . "." . DesignPatternFactory::THUMBNAIL_FORMAT;
		$this->contentUrl = $this->imageRepresentative;
	}

	/**
	 * @throws ImagickException
	 */
	private function uploadPDF(): void
	{
		// GENERATE THUMBNAILS
		$this->imageRepresentative = $this->imageProcessor->generateThumbnailPDF($this->file, $this->directoryDestination);
		// CONTENT URL
		$this->contentUrl = ConfigurationDomain::getHost() . DIRECTORY_SEPARATOR . $this->newFileNamePath . "." . $this->fileExtension;
		// MOVE FILE
		move_uploaded_file(
			$this->file->getFilePath(),
			$_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->newFileNamePath . "." . $this->fileExtension
		);
	}
}
