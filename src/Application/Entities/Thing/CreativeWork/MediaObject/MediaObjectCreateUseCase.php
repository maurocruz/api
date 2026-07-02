<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject;

use Exception;
use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkCreateUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Domain\FileSystem\FileSystemDomain;
use Slim\Psr7\UploadedFile;

class MediaObjectCreateUseCase extends CreativeWorkCreateUseCase
{

	/**
	 * @throws Exception
	 */
	public function create(array $sanitizedParams, array $filesUpload = []): array
	{
		$uploadFile = $filesUpload['uploadfile'] ?? null;

		// VALIDAR ENTRADAS
		if (empty($uploadFile)) {
			return ['status' => false, 'message' => 'Upload file was not found.'];
		}

		// VERIFICAR SE É UM OU MUITOS ARQUIVOS PARA UPLOAD
		$uploadFiles = ($uploadFile instanceof UploadedFile) ? [$uploadFile] : $uploadFile;

		// VALIDAR SE FILES IS UPLOADED
		if (FileSystemDomain::isFileUploaded($uploadFiles) === false) {
			return ['status' => false, 'message' => 'Upload files was failled.'];
		}
		// FAZER UPLOAD
		$results = [];
		foreach ($uploadFiles as $uploadFile) {
			$result = $this->processSingleFile($uploadFile, $sanitizedParams);
			if ($result['status'] === false) {
				return $result;
			} else {
				$results[] = $result['data'];
			}
		}
		if (!empty($results)) {
			return $results;
		}
		return ['status' => false, 'message' => 'Upload failed.', 'data' => []];
	}

	/**
	 * @throws Exception
	 */
	private function processSingleFile(UploadedFile $uploadFile, array $sanitizedParams): array
	{
		$this->fileProcessor->setUploadFile($uploadFile);
		if (isset($sanitizedParams['location'])) {
			$this->fileProcessor->setDirectoryDestination($sanitizedParams['location']);
		}

		// CAPTURAR METADADOS DO ARQUIVO
		$metadata = $this->fileProcessor->getMetadata();
		$paramsSanitized = array_merge($sanitizedParams, $metadata, [
			'type' => $this->resolveMediaType($this->fileProcessor->getFileType()),
			'contentSize' => $this->fileProcessor->getFileSize() ?? null,
			'encodingFormat' => $this->fileProcessor->getFileType() ?? null,
			'uploadDate' => date('Y-m-d H:i:s')
		]);

		// UPLOAD FILE
		$this->fileProcessor->uploadFile();
		$paramsSanitized['image'] = $this->fileProcessor->getImageRepresentative();
		$paramsSanitized['contentUrl'] = $this->fileProcessor->getContentUrl();
		$paramsSanitized['url'] = $this->fileProcessor->getContentUrl();

		$microtimeString = str_replace('.', '', (string)microtime(true));
		$paramsSanitized['name'] = $paramsSanitized['name'] ?? date('Ymd-His') . "_" . (substr($microtimeString, -4)) . "_" . $this->fileProcessor->getFileName();

		// SALVAR CREATIVE WORK
		$saveCreativeWork = parent::create($paramsSanitized);
		if ($saveCreativeWork['status'] === false) {
			return ['status' => false, 'message' => 'Failed to create CreativeWork', 'data' => $saveCreativeWork['data']];
		}
		$creativeWorkData = $saveCreativeWork['data'][0];
		$idthing = $creativeWorkData['thing'];
		$creativeWork = $creativeWorkData['idcreativeWork'];

		// SALVAR MEDIA OBJECT
		$hidratedParams = SupportApplication::hydrateOwnProperties('mediaObject',$paramsSanitized);
		$saveMediaObject = $this->databaseActions->create('mediaObject', $hidratedParams + ['creativeWork' => $creativeWork, 'thing' => $idthing]);
		if($saveMediaObject['status'] === false) {
			return ['status' => false, 'message' => 'Failed to create MediaObject', 'data' => $saveMediaObject['data']];
		}
		$mediaObjectData = $saveMediaObject['data'][0];
		$mediaObject = $mediaObjectData['idmediaObject'];

		// VERIFICAR SE ENCODING FORMAT NÃO É FILHO DE MEDIA OBJECT (IMAGE, AUDIO OU VIDEO) E SALVAR FILHO, CASO SEJA
		$childTable = match ($paramsSanitized['type']) {
			'ImageObject' => 'imageObject',
			'AudioObject' => 'audioObject',
			'VideoObject' => 'videoObject',
			default => null,
		};

		$saveChild = [];
		if ($childTable !== null) {
			$hidratedParams = SupportApplication::hydrateOwnProperties($childTable,$paramsSanitized);
			$saveChild = $this->databaseActions->create($childTable, $hidratedParams + [
					'thing' => $idthing,
					'creativeWork' => $creativeWork,
					'mediaObject' => $mediaObject,
				]);
		}

		if (!empty($saveChild) && $saveChild['status'] === false) {
			$status = false;
			$message = 'Child item has not created.';
			$data =$saveChild['data'];
		} else {
			$status = true;
			$message = 'MediaObject has created.';
			$data = $creativeWorkData + $mediaObjectData;
		}
		return SupportApplication::returnDTO($status, $message, $data);
	}

	private function resolveMediaType(string $mimeType): string
	{
		return match (true) {
			str_starts_with($mimeType, 'image/') => 'ImageObject',
			str_starts_with($mimeType, 'audio/') => 'AudioObject',
			str_starts_with($mimeType, 'video/') => 'VideoObject',
			default => 'MediaObject',
		};
	}
}
