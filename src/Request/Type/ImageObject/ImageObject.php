<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\ImageObject;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Relationship\Relationship;
use Plinct\Tool\ToolBox;

class ImageObject extends ImageObjectAbstract
{
  /**
   * @var string
   */
  protected string $type = "ImageObject";
  /**
   * @var array|string[]
   */
  protected array $properties = [ "*" ];
  /**
   * @var array|string[]
   */
  protected array $hasTypes = [ "author" => "Person" ];

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
	  $hasPart = $params['hasPart'] ?? null;
		if ($hasPart) {
			$dataRelational = (new Relationship('thing',$hasPart,'imageObject'))->getRelationship();
			if (!empty($dataRelational)) {
				foreach ($dataRelational as $value) {
					$idimageObject = $value['idimageObject'];
					$data = parent::getData(['idimageObject'=>$idimageObject]);
					$dataImageObject[] = $data[0];
				}
			}
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
		return $newData;
  }

	/**
	 * @throws Exception
	 */
	public function post(?array $params, ?array $uploadedFiles = null): array
	{
		$imagesUpload = $uploadedFiles['imageupload'] ?? null;
		$idHasPart = $params['idHasPart'] ?? $params['idthing'] ?? null;
		$idimageObject = $params['idimageObject'] ?? null;
		$destination = $params['destination'] ?? $params['location'] ?? $params['imageFolder'] ?? '/public/images/';
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
						if ($idHasPart) {
							$idIsPartOf = $saveImageObject['id'];
							parent::saveThingHasImageObject((int)$idHasPart, (int)$idIsPartOf, $params);
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
		if ($idimageObject && $idHasPart) {
			$returns[] = parent::saveThingHasImageObject($idHasPart, $idimageObject, $params);
		}
		// RESPONSE
		if (empty($returns)) {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing($params);
		} else {
			return ApiFactory::response()->message()->success("Files uploaded!", $returns);
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
		$returns = [];
		if ($idimageObject) {
			$dataImageObject = parent::put($params);
			if ($dataImageObject['status'] === 'success') {
				$returns[] = $dataImageObject;
				$idmediaObject = $dataImageObject['data'][0]['mediaObject'] ?? null;
				if ($idmediaObject) {
					$dataMediaObject = ApiFactory::request()->type('mediaObject')->put(['idmediaObject'=>$idmediaObject] + $params)->ready();
					if ($dataMediaObject['status'] === 'success') {
						$returns[] = $dataMediaObject;
						$idcreativeWork = $dataMediaObject['data'][0]['creativeWork'] ?? null;
						if ($idcreativeWork) {
							$dataCreativeWork = ApiFactory::request()->type('creativeWork')->put(['idcreativeWork'=>$idcreativeWork] + $params)->ready();
							if ($dataCreativeWork['status'] === 'success') {
								$returns[] = $dataCreativeWork;
								$idthing = $dataCreativeWork['data'][0]['thing'] ?? null;
								if ($idthing) {
									$dataThing = ApiFactory::request()->type('thing')->put(['idthing'=>$idthing] + $params)->ready();
									if ($dataThing['status'] === 'success') {
										$returns[] = $dataThing;
									}
								}
							}
						}
					}
				}
			}
			if ($idHasPart) {
				$dataRelationship = (new Relationship('thing', (int) $idHasPart, 'imageObject', (int) $idimageObject))->putRelationship($params);
				$returns[] = $dataRelationship;
			}
		}
		return ["status"=>"success","message"=>"Items updated","data"=>$returns];
  }

	public function delete(array $params): array
	{
		$idimageObject = $params['idimageObject'] ?? null;
		if($idimageObject) {
			$dataImageObject = ApiFactory::request()->type('imageObject')->get(['idimageObject'=>$idimageObject])->ready();
			if (!empty($dataImageObject)) {
				$idthing = ToolBox::searchByValue($dataImageObject[0]['identifier'],'idthing','value');
				return ApiFactory::request()->type('thing')->delete(['idthing'=>$idthing])->ready();
			} else {
				return ApiFactory::response()->message()->fail()->generic($params,'Object not found!');
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing($params);
		}
	}
}
