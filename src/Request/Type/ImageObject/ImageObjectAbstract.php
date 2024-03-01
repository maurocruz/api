<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\ImageObject;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\HttpRequestInterface;
use Plinct\Api\Request\Server\Relationship\Relationship;
use Plinct\Tool\FileSystem\FileSystem;
use Plinct\Tool\Image\Image;

abstract class ImageObjectAbstract extends Entity implements HttpRequestInterface
{
	/**
	 * @param array $imagesUpload
	 * @param string $destination
	 * @return array
	 */
	protected function uploadFiles(array $imagesUpload, string $destination): array
	{

		// TODO limitar max width da imagem


		$fileSystem = new FileSystem($destination);
		// destination dir
		if(!$destination) {
			$uploadsFolder = '/public/uploads/images';
			$imagesFolder = '/public/images';
			if ($fileSystem->file_exists($uploadsFolder)) {
				$destination = $imagesFolder;
			} elseif ($fileSystem->file_exists($imagesFolder)) {
				$destination = $uploadsFolder;
			} else {
				mkdir($_SERVER['DOCUMENT_ROOT'].$imagesFolder,0755,true);
				$destination = $uploadsFolder;
			}
		} elseif (!$fileSystem->getDir()) {
			mkdir($_SERVER['DOCUMENT_ROOT'].$destination,0755,true);
		}
		$fileSystem->setDir($destination);
		// upload images
		return $fileSystem->uploadFiles($imagesUpload);
	}

	/**
	 * @throws Exception
	 */
	protected function saveImageObject(string $pathfile, ?array $params = []): array
	{
		$params = $params ?? [];

		// TODO fazer thumbnails: tiny; small; meddium; large

		$sourceImage = str_replace($_SERVER['DOCUMENT_ROOT'], '', $pathfile);
		$image = new Image($sourceImage);
		$contentUrl = $image->getSrc();
		$params['contentSize'] = $image->getFileSize();
		$params['encodingFormat'] = $image->getEncodingFormat();
		$params['width'] = $image->getWidth();
		$params['height'] = $image->getHeight();
		$params['uploadDate'] = date('Y-m-d H:i:s');

		// create thing
		$dataThing = ApiFactory::request()->type('thing')->post(['name'=>$contentUrl,'type'=>'imageObject'] + $params)->ready();
		if (isset($dataThing['id'])) {
			// create creativework
			$idthing = $dataThing['id'];
			$dataCreativeWork = ApiFactory::request()->type('creativeWork')->post(['thing'=>$idthing,'name'=>$contentUrl] + $params)->ready();
			if (isset($dataCreativeWork['id'])) {
				$idcreativeWork = $dataCreativeWork['id'];
				$dataMediaObject = ApiFactory::request()->type('mediaObject')->post(['creativeWork'=>$idcreativeWork,'contentUrl'=>$contentUrl] + $params)->ready();
				if (isset($dataMediaObject['id'])) {
					$idMediaObject = $dataMediaObject['id'];
					return parent::post(['mediaObject'=>$idMediaObject] + $params);
				} else {
					return ApiFactory::response()->message()->error()->anErrorHasOcurred($dataMediaObject);
				}
			} else {
				return ApiFactory::response()->message()->error()->anErrorHasOcurred($dataCreativeWork);
			}
		}
		return ApiFactory::response()->message()->error()->anErrorHasOcurred($dataThing);
	}

	/**
	 * @param int $idHasPart
	 * @param int $idimageObject
	 * @param array $params
	 * @return false|string[]
	 */
	protected function saveThingHasImageObject(int $idHasPart, int $idimageObject, array $params)
	{
		$relationShip = new Relationship('thing', $idHasPart,'imageObject', $idimageObject);
		return $relationShip->post($params);
	}
}
