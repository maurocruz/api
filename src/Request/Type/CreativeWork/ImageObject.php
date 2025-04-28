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
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$idHasPart = $params['idHasPart'] ?? null;
		$fields = $params['fields'] ?? null;
	  $isPartOf = $params['isPartOf'] ?? null;
		$idimageObject = $params['idimageObject'] ?? null;
	  unset($params['isPartOf']);
	  unset($params['idHasPart']);
		// IF IMAGE OBJECT IS PART OF
		if ($idHasPart) {
			$getData = new GetData('thing_has_imageObject');
			$getData->setFields('*,thing_has_imageObject.caption,thing_has_imageObject.href,thing_has_imageObject.position');
			$getData->setLeftJoin('imageObject','`thing_has_imageObject`.idimageObject=`imageObject`.idimageObject');
			$getData->setLeftJoin('mediaObject','`mediaObject`.idmediaObject=`imageObject`.mediaObject');
			$getData->setLeftJoin('creativeWork','`creativeWork`.idcreativeWork=`mediaObject`.creativeWork');
			$getData->setLeftJoin('thing','`thing`.idthing=`imageObject`.thing');
			$getData->setParams($params + ['where'=>"`thing_has_imageObject`.idthing=$idHasPart"]);
			$data = $getData->render();
		}
		// HAS PART
		else if ($isPartOf && $idimageObject) {
			$getData = new GetData('thing_has_imageObject');
			$getData->setLeftJoin('thing','`thing`.idthing=`thing_has_imageObject`.idthing');
			$getData->setParams($params);
			$data = $getData->render();
		}
		// COUNT
		else if ($fields == 'count') {
			$getData = new GetData('imageObject', false);
			$getData->setFields("count(idimageObject) as count");
			$data = $getData->render();
		} else {
			$getData = new GetData('imageObject');
			$getData->setLeftJoin('mediaObject','`mediaObject`.idmediaObject=`imageObject`.mediaObject');
			$getData->setLeftJoin('creativeWork','`creativeWork`.idcreativeWork=`mediaObject`.creativeWork');
			if ($idimageObject) $getData->setWhere("idimageObject=$idimageObject");
			$getData->setParams($params);
			$data = $getData->render();
		}
		foreach ($data as $key => $value) {
			if (isset($value['representativeOfPage'])) {
				$data[$key]['representativeOfPage'] = !!$value['representativeOfPage'];
			}
			if ($isPartOf && $idimageObject) {
				$typeHasPart = lcfirst($value['type']);
				$idHasPart = $value['idthing'];
				$dataHasPart = ApiFactory::request()->type($typeHasPart)->get(['thing'=>$idHasPart])->ready();
				if(isset($dataHasPart[0])) {
					$data[$key] = $value + $dataHasPart[0];
				}
			}
			if ($properties) {
				// ABOUT
				if (in_array('mentions', $properties)) {
					$dataGetHasPart = new GetData('thing_has_imageObject');
					$dataGetHasPart->setLeftJoin('thing','`thing`.idthing=`thing_has_imageObject`.idthing');
					$dataGetHasPart->setParams($params);
					$dataHasPart = $dataGetHasPart->render();
					if (isset($dataHasPart[0])) {
						foreach ($dataHasPart as $keyHasPart => $valueHasPart) {
							$typeHasPart = lcfirst($valueHasPart['type']);
							$dataHasPart = ApiFactory::request()->type($typeHasPart)->get(['thing'=>$valueHasPart['idthing']])->ready();
							if(isset($dataHasPart[0])) {
								$data[$key]['mentions'][] = ApiFactory::response()->type($typeHasPart)->setData($dataHasPart[0])->ready();;
							}
						}
					}
				}
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
		$idHasPart = $params['idHasPart'] ?? null;
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
							if ($idHasPart) {
								$idimageObject = $saveImageObject['data'][0]['idimageObject'];
								$returns[] = parent::saveThingHasImageObject((int) $idHasPart, (int) $idimageObject);
							}
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
		else if($idHasPart && $idimageObject) {
			return parent::saveThingHasImageObject((int) $idHasPart, (int) $idimageObject, $params);
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
		$idHasPart = $params['idHasPart'] ?? null;
		if($idimageObject && $idHasPart) {
			// IF RELATIONSHIP
			return parent::updateHasTable($params, $idHasPart);
		} else if ($idimageObject) {
			$dataImageObject = parent::getData(['idimageObject'=>$idimageObject]);
			if (!empty($dataImageObject)) {
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
		$idimageObject = $params['idimageObject'] ?? $params['idIsPartOf'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		if($idHasPart) {
			$deleteReturn =  PDOConnect::crud()->setTable('thing_has_imageObject')->erase(['idthing'=>$idHasPart,'idimageObject'=>$idimageObject]);
			if ($deleteReturn['status'] === 'success') {
				parent::reorderingPosition($idHasPart);
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
