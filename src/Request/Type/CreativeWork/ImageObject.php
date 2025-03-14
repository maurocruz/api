<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Tool\Image\Image;

class ImageObject extends ImageObjectAbstract
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('imageObject');
	}

	/**
   * @param array $params
   * @return array
   * @throws Exception
   */
  public function get(array $params = []): array
  {
		$idHasPart = $params['idHasPart'] ?? $params['isPartOf'] ?? null;
		$fields = $params['fields'] ?? null;
		unset($params['isPartOf']);
		unset($params['idHasPart']);
	  $hasPart = $params['hasPart'] ?? null;
		// IF IMAGE OBJECT IS PART OF
		if ($idHasPart) {
			$getDate = new GetData('thing_has_imageObject');
			$getDate->setParams($params + ['where'=>"`thing_has_imageObject`.idthing=$idHasPart"]);
			$getDate->setLeftJoin('imageObject','`thing_has_imageObject`.idimageObject=`imageObject`.idimageObject');
			$getDate->setLeftJoin('mediaObject','`mediaObject`.idmediaObject=`imageObject`.mediaObject');
			$getDate->setLeftJoin('creativeWork','`creativeWork`.idcreativeWork=`mediaObject`.creativeWork');
			$getDate->setLeftJoin('thing','`thing`.idthing=`imageObject`.thing');
			$data = $getDate->render();
		}
		// HAS PART
		else if ($hasPart) {
			$data = parent::getHasPart($hasPart);
		}
		// COUNT
		else if ($fields == 'count') {
			$getDate = new GetData('imageObject', false);
			$getDate->setFields("count(idimageObject) as count");
			$data = $getDate->render();
		}
		else {
			$getDate = new GetData('imageObject');
			$getDate->setLeftJoin('mediaObject','`mediaObject`.idmediaObject=`imageObject`.mediaObject');
			$getDate->setLeftJoin('creativeWork','`creativeWork`.idcreativeWork=`mediaObject`.creativeWork');
			$getDate->setParams($params);
			$data = $getDate->render();
		}
		foreach ($data as $key => $value) {
			if (isset($value['representativeOfPage'])) {
				$data[$key]['representativeOfPage'] = !!$value['representativeOfPage'];
			}
		}
	  return parent::sortData($data);
  }

	/**
	 * @throws Exception
	 */
	public function post(array $params = null, ?array $uploadedFiles = null): array
	{
		$imagesUpload = $uploadedFiles['imageupload'] ?? null;
		$isPartOf = $params['isPartOf'] ?? $params['thing'] ?? null;
		$idimageObject = $params['idimageObject'] ?? null;
		$destination = $params['destination'] ?? $params['location'] ?? $params['imageFolder'] ?? null;
		$returns = null;
		// UPLOAD FILES
		if ($imagesUpload) {
			$uploadedFilesReturns = parent::uploadFiles($imagesUpload,$destination);
			if ($uploadedFilesReturns['status'] === 'success') {
				foreach ($uploadedFilesReturns['data'] as $fileUploaded) {
					if ($fileUploaded['status'] === 'success') {
						// SAVE NEW IMAGE OBJECT
						$saveImageObject = parent::saveImageObject($fileUploaded['data'], $params);
						if (!empty($saveImageObject) && $saveImageObject['status'] == 'success') {
							$returns[] = ApiFactory::response()->message()->success("ImageObject created and file uploaded", $saveImageObject['data'][0] ?? null);
						} else {
							return ApiFactory::response()->message()->fail()->generic($saveImageObject);
						}
					} elseif ($fileUploaded['status'] === 'fail') {
						return ApiFactory::response()->message()->fail()->generic($fileUploaded, 'Upload failed');
					} else {
						return ApiFactory::response()->message()->error()->anErrorHasOcurred($fileUploaded);
					}
				}
			}
			if($returns) {
				return ApiFactory::response()->message()->success("Uploaded files and create ImageObjects", $returns );
			} else {
				return ApiFactory::response()->message()->fail()->generic([$uploadedFilesReturns]);
			}
		}
		// save relational if not uploded images
		else if($isPartOf && $idimageObject) {
			return parent::saveThingHasImageObject((int) $isPartOf, (int) $idimageObject, $params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing($params);
		}
	}

  /**
   * @param ?array $params
   * @return array
   */
  public function put(array $params = null): array
  {
		$idimageObject = $params['idimageObject'] ?? null;
		$isPartOf = $params['isPartOf'] ?? null;
		if($idimageObject && $isPartOf) {
			// IF RELATIONSHIP
			return parent::updateHasTable($params, $isPartOf);
		} else if ($idimageObject) {
			$dataImageObject = parent::getData(['idimageObject'=>$idimageObject]);
			if (!empty($dataImageObject)) {
				//
				$putImageObject = parent::put($params);
				if ($putImageObject['status'] === 'success') {
					$idmediaObject = $dataImageObject[0]['mediaObject'];
					$putMediaObject = ApiFactory::request()->type('mediaObject')->put(['idmediaObject'=>$idmediaObject] + $params)->ready();
					if ($putMediaObject['status'] === 'success') {
						return ApiFactory::response()->message()->success('ImageObject was updated', [$putImageObject, $putMediaObject]);
					} else {
						return ApiFactory::response()->message()->fail()->generic($putMediaObject);
					}
				}
			} else {
				return ApiFactory::response()->message()->fail()->returnIsEmpty();
			}
		}
		return ApiFactory::response()->message()->fail()->generic(["Mandatory not found: idimageObject in put imageObject"]);
  }

	/**
	 * @param array $params
	 * @return array
	 * @throws Exception
	 */
	public function delete(array $params): array
	{
		$idimageObject = $params['idimageObject'] ?? $params['imageObject'] ?? null;
		$idthing = $params['isPartOf'] ?? $params['idthing'] ?? null;
		if($idthing) {
			$deleteReturn =  PDOConnect::crud()->setTable('thing_has_imageObject')->erase(['idthing'=>$idthing,'idimageObject'=>$idimageObject]);
			if ($deleteReturn['status'] === 'success') {
				parent::reorderingPosition($idthing);
			}
			return $deleteReturn;
		} else if($idimageObject) {
			$dataImageObject = self::get(['idimageObject'=>$idimageObject]);
			if (!empty($dataImageObject)) {
				$valueImageObject = $dataImageObject[0];
				// elimina os arquivos
				$image = new Image($valueImageObject['contentUrl']);
				$pathfile = $image->getPathFile();
				$pathInfo = pathinfo($pathfile);
				$dirname = $pathInfo['dirname'];
				$filename = $pathInfo['filename'];
				$extension = $pathInfo['extension'];
				unlink($pathfile);
				$meddiumFile = $dirname.DIRECTORY_SEPARATOR.$filename."_m.".$extension;
				if (file_exists($meddiumFile)) unlink($meddiumFile);
				$smallFile = $dirname.DIRECTORY_SEPARATOR.$filename."_s.".$extension;
				if(file_exists($smallFile)) unlink($smallFile);
				unlink($dirname.DIRECTORY_SEPARATOR.$filename."_t.".$extension);
				// apaga registro
				return ApiFactory::request()->type('thing')->delete(['idthing'=>$valueImageObject['thing']])->ready();
			} else {
				return ApiFactory::response()->message()->fail()->generic($params,'ImageObject id is not found');
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idimageObject or imageObject and idthing or isPartOf!"]);
		}
	}
}
