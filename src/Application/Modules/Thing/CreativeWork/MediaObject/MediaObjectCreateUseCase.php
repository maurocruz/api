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
		foreach ($uploadFiles as $uploadFile) {
			$this->fileProcessor->setUploadFile($uploadFile);
			if (isset($params['location'])) {
				$this->fileProcessor->setDirectoryDestination($params['location']);
			}

			// UPLOAD FILE
			$this->fileProcessor->uploadFile();
			$params['image'] = $this->fileProcessor->getImageRepresentative();
			$params['contentUrl'] = $this->fileProcessor->getContentUrl();
			$params['url'] = $this->fileProcessor->getContentUrl();
			$microtimeString = str_replace('.', '', (string)microtime(true));
			$params['name'] = $params['name'] ?? date('Ymd-His') . "_" . (substr($microtimeString, -4)) . "_" . $this->fileProcessor->getFileName();

			// CAPTURAR METADADOS DO ARQUIVO
			$metaData = $this->fileProcessor->getMetadata();
			$params['type'] = match ($this->fileProcessor->getFileType()) {
				'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml' => 'ImageObject',
				default => 'MediaObject',
			};
			$params['contentSize'] = $this->fileProcessor->getFileSize() ?? null;
			$params['encodingFormat'] = $this->fileProcessor->getFileType() ?? null;
			$params['uploadDate'] = date('Y-m-d H:i:s');
			$params['author'] = $metaData['Author'] ?? null;
			$params['editor'] = $metaData['Creator'] ?? null;
			$params['publisher'] = $metaData['Producer'] ?? null;
			$params['dateCreated'] = $metaData['CreationDate'] ?? null;
			$params['dateModified'] = $metaData['ModDate'] ?? null;
			$params['size'] = $metaData['Pages'] ?? null;

			// SALVAR CREATIVE WORK
			$saveCreativeWork = parent::create($params);
			if ($saveCreativeWork['status'] === false) {
				return ['status' => false, 'message' => 'CreativeWork item has not created.', 'data' => $saveCreativeWork['data']];
			}
			$creativeWorkData = $saveCreativeWork['data'];
			$idthing = $creativeWorkData['thing'];
			$creativeWork = $creativeWorkData['idcreativeWork'];

			// SALVAR MEDIA OBJECT
			$saveMediaObject = $this->databaseActions->create('mediaObject', $params + ['creativeWork' => $creativeWork, 'thing' => $idthing]);
			if($saveMediaObject['status'] === false) {
				return ['status' => false, 'message' => 'MediaObject item has not created.', 'data' => $saveMediaObject['data']];
			}
			$mediaObjectData = $saveMediaObject['data'];
			$mediaObject = $mediaObjectData['idmediaObject'];

			// VERIFICAR SE ENCODING FORMAT NÃO É FILHO DE MEDIA OBJECT (IMAGE, AUDIO OU VIDEO) E SALVAR FILHO, CASO SEJA
			$saveChild = [];
			if ($params['type'] === 'ImageObject') {
				$saveChild = $this->databaseActions->create('imageObject', $params + ['thing'=>$idthing, 'creativeWork'=> $creativeWork, 'mediaObject' => $mediaObject]);
			}
			if ($params['type'] === 'AudioObject') {
				$saveChild = $this->databaseActions->create('audioObject', $params + ['thing'=>$idthing, 'creativeWork'=> $creativeWork, 'mediaObject' => $mediaObject]);
			}
			if ($params['type'] === 'VideoObject') {
				$saveChild = $this->databaseActions->create('videoObject', $params + ['thing'=>$idthing, 'creativeWork'=> $creativeWork, 'mediaObject' => $mediaObject]);
			}
			if (!empty($saveChild) && $saveChild['status'] === false) {
				return ['status' => false, 'message' => 'Child item has not created.', 'data' => $saveChild['data']];
			} else {
				$dataReturns = $saveCreativeWork['data'] + $saveMediaObject['data'];
				return ['status' => true, 'message' => 'MediaObject item has created', 'data' => $dataReturns];
			}
		}
		return ['status' => false, 'message' => 'Upload files was failled.'];
	}
}
