<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\HttpRequestInterface;
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
		$isPartOf = $params['isPartOf'] ?? $params['thing'] ?? null;
		unset($params['isPartOf']); // para não dar erro em chave estrangeira no insert creative work

		// TODO fazer thumbnails: tiny; small; meddium; large

		$sourceImage = str_replace($_SERVER['DOCUMENT_ROOT'], '', $pathfile);
		$image = new Image($sourceImage);
		$params['contentUrl'] = $image->getSrc();
		$params['contentSize'] = $image->getFileSize();
		$params['encodingFormat'] = $image->getEncodingFormat();
		$params['width'] = $image->getWidth();
		$params['height'] = $image->getHeight();
		$params['name'] = $params['name'] ?? $image->getBasename();
		// SAVE MEDIAOBJECT
		$postMediaObject = ApiFactory::request()->type('mediaObject')->post($params)->ready();
		if (isset($postMediaObject['id'])) {
			$idMediaObject = $postMediaObject['id'];
			// SAVE IMAGEOBJECT
			$dataImageObject = parent::post(['mediaObject'=>$idMediaObject] + $params);
			if ($isPartOf) {
				$idIsPartOf = $dataImageObject['id'];
				self::saveThingHasImageObject((int)$isPartOf, (int)$idIsPartOf, $params);
			}
			return  $dataImageObject;
		}
		return ApiFactory::response()->message()->error()->anErrorHasOcurred($postMediaObject);
	}

	/**
	 * @param int $idthing
	 * @param int $idimageObject
	 * @param array $params
	 * @return string[]
	 */
	protected function saveThingHasImageObject(int $idthing, int $idimageObject, array $params): array
	{
		return PDOConnect::crud()->setTable('thing_has_imageObject')->created(['idthing'=>$idthing, 'idimageObject'=>$idimageObject] + $params);
	}

	/**
	 * @param array $params
	 * @param string $isPartOf
	 * @return array
	 */
	public function updateHasTable(array $params, string $isPartOf): array
	{
		$idimageObject = (int) $params['idimageObject'];
		$position = isset($params['position']) ? (int) $params['position'] : null;
		$representativeOfPage = $params['representativeOfPage'] ?? null;
		$caption = $params['caption'] ?? null;
		$dataItem = PDOConnect::crud()->setTable('thing_has_imageObject')->read(['where'=>"`idimageObject`='$idimageObject' AND `idthing`='$isPartOf'"]);
		$idthing = $dataItem[0]['idthing'];
		// representative of page
		if ($representativeOfPage ) {
			PDOConnect::crud()->setTable('thing_has_imageObject')->update(['representativeOfPage'=>0],"`idthing`='$isPartOf'");
			PDOConnect::crud()->setTable('thing_has_imageObject')->update(['representativeOfPage'=>$representativeOfPage],"`idimageObject`='$idimageObject'");
		}
		// position
		if ($position) {
			$oldPosition = $dataItem[0]['position'];
			$dataAll = PDOConnect::crud()->setTable('thing_has_imageObject')->read(['where'=>"`idthing`='$idthing'"]);
			foreach ($dataAll as $value) {
				$currentPosition = $value['position'];
				$id = $value['idimageObject'];
				if ($id === $idimageObject) {
					$newPosition = $position;
				} else if ($position < $oldPosition && $currentPosition >= $position && $currentPosition < $oldPosition) {
				  $newPosition = $currentPosition + 1;
			  } else if ($position > $oldPosition && $currentPosition <= $position && $currentPosition > $oldPosition) {
					$newPosition = $currentPosition - 1;
				} else {
					$newPosition = $currentPosition;
				}
				PDOConnect::crud()->setTable('thing_has_imageObject')->update(['position'=>$newPosition],"`idimageObject`='$id'");
			}
			// reordering position in things_has_imageObject
			$connect = ApiFactory::request()->server()->connectBd('thing_has_imageObject');
			$newData = $connect->read(['where' => "`idthing`='$idthing'", "orderBy" => "position"]);
			foreach ($newData as $k => $v) {
				PDOConnect::crud()->setTable('thing_has_imageObject')->update(['position' => $k + 1], "`idimageObject`='{$v['idimageObject']}'");
			}
		}
		// caption 
		if ($caption) {
			PDOConnect::crud()->setTable('thing_has_imageObject')->update(['caption'=>$caption],"`idimageObject`='$idimageObject'");
		}
		return ApiFactory::response()->message()->success("thing has imageObject has updated");
	}

	/**
	 * @param $isPartOf
	 * @return void
	 */
	protected function reorderingPosition($isPartOf)
	{
		$connect = ApiFactory::request()->server()->connectBd('thing_has_imageObject');
		$newData = $connect->read(['where' => "`idthing`='$isPartOf'", "orderBy" => "position"]);
		foreach ($newData as $k => $v) {
			PDOConnect::crud()->setTable('thing_has_imageObject')->update(['position' => $k + 1], "`idimageObject`='{$v['idimageObject']}'");
		}
	}

	/**
	 * @param string $idimageObject
	 * @return array
	 */
	protected function getHasPart(string $idimageObject): array
	{
		$data = PDOConnect::crud()->setTable('thing_has_imageObject')->read(['where'=>"`idimageObject`='$idimageObject'"]);
		$newData = [];
		foreach ($data as $value) {
			$newData[] = ApiFactory::request()->type('thing')->get(['idthing'=>$value['idthing']])->ready()[0];
		}
		return $newData;
	}
}
