<?php
namespace Plinct\Api\Application\Modules\Thing\CreativeWork\MediaObject;

use Exception;
use Plinct\Api\Application\Modules\Thing\CreativeWork\CreativeWorkCreateUseCase;
use Slim\Psr7\UploadedFile;

class MediaObjectCreateUseCase extends CreativeWorkCreateUseCase
{

	/**
	 * @throws Exception
	 */
	public function create(array|null $params = [], array $filesUpload = []): array
	{
		$uploadFile = $filesUpload['uploadfile'] ?? null;
		$uploadFiles = $filesUpload['uploadfiles'] ?? null;
		// IF UPLOAD FILE
		if ($uploadFile) {
			return $this->saveWithUploadFile($params, $uploadFile);
		}
		// IF UPLOAD FILES
		elseif ($uploadFiles) {
			return $this->saveWithUploadFiles($params, $uploadFiles);
		} else {
			return ['status' => false, 'message' => 'No input data was found.'];
		}
	}

	/**
	 * @throws Exception
	 */
	private function saveWithUploadFile(array|null $params, UploadedFile $uploadFile = null): array
	{
		if ($uploadFile) {
			$params = $this->uploadFile($params, $uploadFile);
		}

		// SAVE CREATIVE WORK
		$saveCreativeWork = parent::create($params);
		if ($saveCreativeWork['status'] === false) {
			return $saveCreativeWork;
		} else {
			$creativeWorkData = $saveCreativeWork['data'];
			$thing = $creativeWorkData['thing'];
			$creativeWork = $creativeWorkData['idcreativeWork'];

			$saveMediaObject = $this->databaseActions->create('mediaObject', $params + ['creativeWork' => $creativeWork, 'thing' => $thing]);

			if($saveMediaObject['status'] === false) {
				return $saveMediaObject;
			} else {
				return ['status' => true, 'message' => 'MediaObject item has created', 'data' => $saveMediaObject['data']];
			}
		}
	}

	/**
	 * @throws Exception
	 */
	private function saveWithUploadFiles(array|null $params, array $uploadFiles): array
	{
		$status = false;
		$returns = [];
		foreach ($uploadFiles as $uploadFile) {
			$params = $this->uploadFile($params, $uploadFile);
			// SAVE CREATIVE WORK
			$saveCreativeWork = parent::create($params);
			if ($saveCreativeWork['status'] === false) {
				$returns[] = $saveCreativeWork;
			} else {
				$status = true;
				$creativeWorkData = $saveCreativeWork['data'];
				$thing = $creativeWorkData['thing'];
				$creativeWork = $creativeWorkData['idcreativeWork'];

				$saveMediaObject = $this->databaseActions->create('mediaObject', $params + ['creativeWork' => $creativeWork, 'thing' => $thing]);

				if($saveMediaObject['status'] === false) {
					$returns[] = $saveMediaObject;
				} else {
					$returns[] = $saveMediaObject['data'];
				}
			}
		}
		$message = $status ? 'Media Objects Created' : 'Media Objects Not Created';
		return ['status' => $status, 'message' => $message, 'data' => $returns];

	}

	/**
	 * @throws Exception
	 */
	private function uploadFile(array|null $params, UploadedFile $uploadFile): array
	{
		$this->fileProcessor->setUploadFile($uploadFile);
		$metaData = $this->fileProcessor->getMetadata();
		$params['type'] = match ($this->fileProcessor->getFileType()) {
			'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'  => 'ImageObject',
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
		// UPLOAD FILE
		$this->fileProcessor->uploadFile($uploadFile);
		$params['image'] = $this->fileProcessor->getImageRepresentative();
		$params['contentUrl'] = $this->fileProcessor->getContentUrl();
		$params['url'] = $this->fileProcessor->getContentUrl();
		$params['name'] = $params['name'] ?? date('Ymd-His')."_".microtime(true)."_".$this->fileProcessor->getFileName();
		return $params;
	}
}
