<?php
namespace Plinct\Api\Application\Modules\Thing\CreativeWork\MediaObject;

use Exception;
use Plinct\Api\Application\Modules\Thing\CreativeWork\CreativeWorkCreateUseCase;
use Plinct\Api\Domain\FileSystem\FileSystemDomain;
use Slim\Psr7\UploadedFile;

class MediaObjectCreateUseCase extends CreativeWorkCreateUseCase
{

	/**
	 * @throws Exception
	 */
	public function create(array $params = [], array $filesUpload = []): array
	{
		$uploadFile = $filesUpload['uploadfile'] ?? null;

		// VALIDAR ENTRADAS
		if (empty($uploadFile)) {
			return ['status' => false, 'message' => 'Upload file was not found.'];
		}

		// VERIFICAR SE É UM OU MUITOS ARQUIVOS
		$uploadFiles = ($uploadFile instanceof UploadedFile) ? [$uploadFile] : $uploadFile;

		// VALIDAR SE FILES IS UPLOADED
		if (FileSystemDomain::isFileUploaded($uploadFiles) === false) {
			return ['status' => false, 'message' => 'Upload files was failled.'];
		}
		// FAZER UPLOAD
		$results = [];
		foreach ($uploadFiles as $uploadFile) {
			$result = $this->processSingleFile($uploadFile, $params);
			if ($result['status'] === false) {
				return $result;
			} else {
				$results[] = $result;
			}
		}
		if (!empty($results)) {
			return ['status' => true, 'message' => 'Upload files was success.', 'data' => $results];
		}
		return ['status' => true, 'message' => 'Upload failed.'];
	}

	/**
	 * @throws Exception
	 */
	private function processSingleFile(UploadedFile $uploadFile, array $params): array
	{
		$this->fileProcessor->setUploadFile($uploadFile);
		if (isset($params['location'])) {
			$this->fileProcessor->setDirectoryDestination($params['location']);
		}

		// CAPTURAR METADADOS DO ARQUIVO
		$metadata = $this->fileProcessor->getMetadata();
		$params = array_merge($params, $metadata, [
			'type' => $this->resolveMediaType($this->fileProcessor->getFileType()),
			'contentSize' => $this->fileProcessor->getFileSize() ?? null,
			'encodingFormat' => $this->fileProcessor->getFileType() ?? null,
			'uploadDate' => date('Y-m-d H:i:s')
		]);

		// UPLOAD FILE
		$this->fileProcessor->uploadFile();
		$params['image'] = $this->fileProcessor->getImageRepresentative();
		$params['contentUrl'] = $this->fileProcessor->getContentUrl();
		$params['url'] = $this->fileProcessor->getContentUrl();

		$microtimeString = str_replace('.', '', (string)microtime(true));
		$params['name'] = $params['name'] ?? date('Ymd-His') . "_" . (substr($microtimeString, -4)) . "_" . $this->fileProcessor->getFileName();

		// SALVAR CREATIVE WORK
		$saveCreativeWork = parent::create($params);
		if ($saveCreativeWork['status'] === false) {
			return ['status' => false, 'message' => 'Failed to create CreativeWork', 'data' => $saveCreativeWork['data']];
		}
		$creativeWorkData = $saveCreativeWork['data'];
		$idthing = $creativeWorkData['thing'];
		$creativeWork = $creativeWorkData['idcreativeWork'];

		// SALVAR MEDIA OBJECT
		$saveMediaObject = $this->databaseActions->create('mediaObject', $params + ['creativeWork' => $creativeWork, 'thing' => $idthing]);
		if($saveMediaObject['status'] === false) {
			return ['status' => false, 'message' => 'Failed to create MediaObject', 'data' => $saveMediaObject['data']];
		}
		$mediaObjectData = $saveMediaObject['data'];
		$mediaObject = $mediaObjectData['idmediaObject'];

		// VERIFICAR SE ENCODING FORMAT NÃO É FILHO DE MEDIA OBJECT (IMAGE, AUDIO OU VIDEO) E SALVAR FILHO, CASO SEJA
		$childTable = match ($params['type']) {
			'ImageObject' => 'imageObject',
			'AudioObject' => 'audioObject',
			'VideoObject' => 'videoObject',
			default => null,
		};

		$saveChild = [];
		if ($childTable !== null) {
			$saveChild = $this->databaseActions->create($childTable, $params + [
					'thing' => $idthing,
					'creativeWork' => $creativeWork,
					'mediaObject' => $mediaObject,
				]);
		}

		if (!empty($saveChild) && $saveChild['status'] === false) {
			return ['status' => false, 'message' => 'Child item has not created.', 'data' => $saveChild['data']];
		} else {
			$dataReturns = $saveCreativeWork['data'] + $saveMediaObject['data'];
			return ['status' => true, 'message' => 'MediaObject create successfully', 'data' => $dataReturns];
		}
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
