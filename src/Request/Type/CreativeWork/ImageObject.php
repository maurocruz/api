<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Relationship\Relationship;

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
	  $newData = [];
	  $dataImageObject = [];
	  $isPartOf = $params['isPartOf'] ?? null;
		if ($isPartOf) {
			$dataRelational = (new Relationship('thing',(int) $isPartOf,'imageObject'))->getRelationship();
			if (!empty($dataRelational)) {
				foreach ($dataRelational as $value) {
					$idimageObject = $value['idimageObject'];
					$data = parent::getData(['idimageObject'=>$idimageObject]);
					$dataImageObject[] = $data[0];
				}
			}
			unset($params['isPartOf']);
		} else {
			$dataImageObject = parent::getData($params);
		}
		//
	  foreach ($dataImageObject as $item) {
		  $idmediaObject = $item['mediaObject'];
		  // mediaObject
		  $dataMediaObject = ApiFactory::request()->type('mediaObject')->get(['idmediaObject' => $idmediaObject] + $params)->ready();
		  $idcreativeWork = $dataMediaObject[0]['creativeWork'];
		  // creativeWork
		  $dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork' => $idcreativeWork] + $params)->ready();
		  $idthing = $dataCreativeWork[0]['thing'];
		  // thing
		  $dataThing = ApiFactory::request()->type('thing')->get(['idthing' => $idthing] + $params)->ready();
		  // returns
		  $returns = $item + $dataThing[0] + $dataCreativeWork[0] + $dataMediaObject[0];
		  // new data
		  $newData[] = $returns;
	  }
	  return parent::array_sort($newData, $params);
  }

	/**
	 * @throws Exception
	 */
	public function post(array $params = null, ?array $uploadedFiles = null): array
	{
		$name = $params['name'] ?? null;
		if (!$name) {
			return ApiFactory::response()->message()->fail()->generic(['Mandatory field: name']);
		}
		$imagesUpload = $uploadedFiles['imageupload'] ?? null;
		$isPartOf = $params['isPartOf'] ?? $params['thing'] ?? null;
		$idimageObject = $params['idimageObject'] ?? null;
		$destination = $params['destination'] ?? $params['location'] ?? $params['imageFolder'] ?? '/public/images/';
		$params['additionalType'] = "ImageObject";
		$returns = [];
		// UPLOAD FILES
		if ($imagesUpload) {
			$uploadedFilesReturns = parent::uploadFiles($imagesUpload,$destination);
			foreach ($uploadedFilesReturns as $fileUploaded) {
				if (!empty($fileUploaded)) {
					$pathfile = str_replace($_SERVER['DOCUMENT_ROOT'], '', $fileUploaded['data']);
					// SAVE NEW IMAGE OBJECT
					$saveImageObject = parent::saveImageObject($pathfile, $params);
					if (isset($saveImageObject['id'])) {
						if ($isPartOf) {
							$idIsPartOf = $saveImageObject['id'];
							parent::saveThingHasImageObject((int)$isPartOf, (int)$idIsPartOf, $params);
						}
						$returns[] = ApiFactory::response()->message()->success("File uploaded", ['contentUrl' => $pathfile]);
					} else {
						$returns[] = ApiFactory::response()->message()->fail()->generic($saveImageObject);
					}
				} else {
					$returns[] = ApiFactory::response()->message()->fail()->generic($fileUploaded, 'Upload failed');
				}
			}
		}
		// RELATIONSHIP
		if ($idimageObject && $isPartOf) {
			$returns[] = parent::saveThingHasImageObject($isPartOf, $idimageObject, $params);
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
		$returns = [];
		if ($idimageObject) {
			$dataImageObject = parent::getData(['idimageObject'=>$idimageObject]);
			if (!empty($dataImageObject)) {
				//
				$putImageObject = parent::put($params);
				if ($putImageObject['status'] === 'success') {
					$idmediaObject = $dataImageObject[0]['mediaObject'];
					$putMediaObject = ApiFactory::request()->type('mediaObject')->put(['idmediaObject'=>$idmediaObject] + $params)->ready();
					if ($putMediaObject['status'] === 'success') {
						// IF RELATIONSHIP
						$putHasTable = parent::updateHasTable($params);
						return ApiFactory::response()->message()->success('ImageObject was updated', [$putImageObject, $putMediaObject, $putHasTable]);
					}
				}
			} else {
				return ApiFactory::response()->message()->fail()->returnIsEmpty();
			}
		}
		return ["status"=>"success","message"=>"Items updated","data"=>$returns];
  }

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		$idimageObject = $params['idimageObject'] ?? $params['imageObject'] ?? null;
		if($idimageObject) {
			$dataImageObject = parent::getData(['idimageObject'=>$idimageObject]);
			if (!empty($dataImageObject)) {
				return ApiFactory::request()->type('mediaObject')->delete(['idmediaObject'=>$dataImageObject[0]['mediaObject']])->ready();
			} else {
				return ApiFactory::response()->message()->fail()->generic($params,'ImageObject id is not found');
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idimageObject or imageObject"]);
		}
	}
}
