<?php
namespace Plinct\Api\Application\Modules\Thing\CreativeWork\MediaObject;

use Exception;
use Plinct\Api\Application\Modules\Thing\CreativeWork\CreativeWorkCreateUseCase;

class MediaObjectCreateUseCase extends CreativeWorkCreateUseCase
{

	/**
	 * @throws Exception
	 */
	public function create(array $params = [], array $filesUpload = []): array
	{
		if (!isset($params['name'])) {
			return ['status' => false, 'message' => 'Name is required'];
		}
		// UPLOAD FILE
		$uploadFiles = $filesUpload['uploadfile'] ?? null;
		if ($uploadFiles) {
			$this->fileProcessor->setUploadFile($uploadFiles);
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
			$this->fileProcessor->uploadFile($uploadFiles);
			$params['image'] = $this->fileProcessor->getImageRepresentative();
			$params['contentUrl'] = $this->fileProcessor->getContentUrl();
			$params['url'] = $this->fileProcessor->getContentUrl();
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
}
