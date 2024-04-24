<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
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
		$idimageObject = $params['idimageObject'] ?? null;
	  $isPartOf = $params['isPartOf'] ?? null;
	  $orderBy = $params['orderBy'] ?? null;
	  $ordering = $params['ordering'] ?? "asc";
	  $hasPart = $params['hasPart'] ?? null;
		// IS PART OF
		if ($isPartOf) {
			$query = "SELECT *, thing_has_imageObject.position FROM `thing_has_imageObject`"
			. " LEFT JOIN `imageObject` ON `thing_has_imageObject`.`idimageObject`=`imageObject`.`idimageObject`"
			. " RIGHT JOIN `mediaObject` ON `mediaObject`.`idmediaObject`=`imageObject`.`mediaObject`"
			. " RIGHT JOIN `creativeWork` ON creativeWork.idcreativeWork=mediaObject.creativeWork"
			. " LEFT JOIN thing ON thing.idthing=`imageObject`.`thing`"
			. " WHERE `thing_has_imageObject`.`idthing`='$isPartOf'";
			if ($orderBy) {
				$query .= " ORDER BY `thing_has_imageObject`.$orderBy $ordering";
			}
			$query .= ";";
			$data = PDOConnect::run($query);
			return parent::sortData($data);
		} else if ($hasPart) {
			return parent::getHasPart($idimageObject);
		}  else {
			$data = parent::getData($params);
			return parent::sortData($data);
		}
  }

	/**
	 * @throws Exception
	 */
	public function post(array $params = null, ?array $uploadedFiles = null): array
	{
		$imagesUpload = $uploadedFiles['imageupload'] ?? null;
		$isPartOf = $params['isPartOf'] ?? $params['thing'] ?? null;
		$idimageObject = $params['idimageObject'] ?? null;
		$destination = $params['destination'] ?? $params['location'] ?? $params['imageFolder'] ?? '/public/images/';
		$params['type'][] = "ImageObject";
		$returns = [];
		// UPLOAD FILES
		if ($imagesUpload) {
			$uploadedFilesReturns = parent::uploadFiles($imagesUpload,$destination);
			foreach ($uploadedFilesReturns as $fileUploaded) {
				if ($fileUploaded['status'] === 'success') {
					$pathfile = str_replace($_SERVER['DOCUMENT_ROOT'], '', $fileUploaded['data']);
					// SAVE NEW IMAGE OBJECT
					$saveImageObject = parent::saveImageObject($pathfile, $params);
					if (isset($saveImageObject[0])) {
						$idimageObject = $saveImageObject[0]['idimageObject'];
						$returns[] = ApiFactory::response()->message()->success("File uploaded", ['contentUrl' => $pathfile]);
					} else {
						$returns[] = ApiFactory::response()->message()->fail()->generic($saveImageObject);
					}
				} elseif ($fileUploaded['status'] === 'fail') {
					$returns[] = ApiFactory::response()->message()->fail()->generic($fileUploaded, 'Upload failed');
				} else {
					return ApiFactory::response()->message()->error()->anErrorHasOcurred($fileUploaded);
				}
			}
		}
		// RELATIONSHIP
		if ($idimageObject && $isPartOf) {
			$returns[] = parent::saveThingHasImageObject((int) $isPartOf, (int) $idimageObject, $params);
		}
		// RESPONSE
		if (empty($returns)) {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing($params);
		} else {
			return $returns;
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
		$isPartOf = $params['isPartOf'] ?? null;
		if($idimageObject && $isPartOf) {
			$deleteReturn =  PDOConnect::crud()->setTable('thing_has_imageObject')->erase(['idthing'=>$isPartOf,'idimageObject'=>$idimageObject]);
			if ($deleteReturn['status'] === 'success') {
				parent::reorderingPosition($isPartOf);
			}
			return $deleteReturn;
		} else if($idimageObject) {
			$dataImageObject = self::get(['idimageObject'=>$idimageObject]);
			if (!empty($dataImageObject)) {
				$valueImageObject = $dataImageObject[0];
				// elimina o arquivo
				$image = new Image($valueImageObject['contentUrl']);
				$pathfile = $image->getPathFile();
				unlink($pathfile);
				// apaga registro
				return ApiFactory::request()->type('mediaObject')->delete(['idmediaObject'=>$valueImageObject['mediaObject']])->ready();
			} else {
				return ApiFactory::response()->message()->fail()->generic($params,'ImageObject id is not found');
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idimageObject or imageObject!"]);
		}
	}
}
