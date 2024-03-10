<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
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
		$params['contentUrl'] = $image->getSrc();
		$params['contentSize'] = $image->getFileSize();
		$params['encodingFormat'] = $image->getEncodingFormat();
		$params['width'] = $image->getWidth();
		$params['height'] = $image->getHeight();
		unset($params['isPartOf']);
		// SAVE MEDIAOBJECT
		$dataMediaObject = ApiFactory::request()->type('mediaObject')->post($params)->ready();
		if (isset($dataMediaObject['id'])) {
			$idMediaObject = $dataMediaObject['id'];
			// SAVE IMAGEOBJECT
			return parent::post(['mediaObject'=>$idMediaObject] + $params);
		}
		return ApiFactory::response()->message()->error()->anErrorHasOcurred($dataMediaObject);
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

	/**
	 * @param array $params
	 * @return array
	 */
	public function updateHasTable(array $params): array
	{
		$idimageObject = $params['idimageObject'];
		$newPosition = $params['position'] ?? null;
		$dataItem = PDOConnect::crud()->setTable('thing_has_imageObject')->read(['where'=>"`idimageObject`='$idimageObject'"]);
		$idthing = $dataItem[0]['idthing'];
		// position
		if ($newPosition) {
			$oldPosition = $dataItem[0]['position'];
			$dataAll = PDOConnect::crud()->setTable('thing_has_imageObject')->read(['where'=>"`idthing`='$idthing'"]);
			foreach ($dataAll as $value) {
				$position = $value['position'];
				$id = $value['idimageObject'];
				 if ($newPosition < $oldPosition && $position >= $newPosition && $position < $oldPosition) {
					 PDOConnect::crud()->setTable('thing_has_imageObject')->update(['position'=>$position+1],"`idimageObject`='$id'");
				 } else if ($newPosition > $oldPosition && $position <= $newPosition && $position > $oldPosition) {
					 PDOConnect::crud()->setTable('thing_has_imageObject')->update(['position'=>$position-1],"`idimageObject`='$id'");
				 }
			}
		}
		// update thing
		PDOConnect::crud()->setTable('thing_has_imageObject')->update($params,"`idimageObject`='$idimageObject'");
		// reordering things
		$connect = ApiFactory::request()->server()->connectBd('thing_has_imageObject');
		$newData = $connect->read(['where'=>"`idthing`='$idthing'", "orderBy"=>"position"]);
		foreach ($newData as $k => $v) {
			PDOConnect::crud()->setTable('thing_has_imageObject')->update(['position'=>$k+1],"`idimageObject`='{$v['idimageObject']}'");
		}
		return $dataItem;
	}
}
