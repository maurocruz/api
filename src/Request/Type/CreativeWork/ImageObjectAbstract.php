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
use Plinct\Tool\StringTool;

abstract class ImageObjectAbstract extends Entity implements HttpRequestInterface
{
	/**
	 * @param array $imagesUpload
	 * @param ?string $destination
	 * @return array
	 * @throws Exception
	 */
	protected function uploadFiles(array $imagesUpload, ?string $destination): array
	{
		$uploadsFolder = '/public/uploads/images/';
		// create destination
		$fileSystem = new FileSystem($destination);
		// destination dir
		if(!$destination || $destination == '') {
			if (!$fileSystem->file_exists($uploadsFolder)) {
				mkdir($_SERVER['DOCUMENT_ROOT'] . $imagesFolder, 0755, true);
			}
			$destination = $uploadsFolder;
		} else {
			$destination = $uploadsFolder . ($destination[0] == '/' ? substr($destination, 1) : $destination);
			if (!$fileSystem->file_exists($destination)) {
				mkdir($_SERVER['DOCUMENT_ROOT'] . $destination, 0755, true);
			}
		}
		$destination = str_ends_with($destination, '/') ? $destination : $destination."/";
		$fileSystem->setDir($destination);
		// save images
		if(isset($imagesUpload['error'])) {
			$data = [];
			foreach ($imagesUpload['error'] as $key => $error) {
				if ($error == UPLOAD_ERR_OK) {
					$name = $imagesUpload['name'][$key];
					$type = $imagesUpload['type'][$key];
					$tmp_name = $imagesUpload['tmp_name'][$key];
					$newImage = new Image($tmp_name);
					$width = $newImage->getWidth();
					$ratio = 1.618; // number gold
					$largeWidth = 1280;
					$meddiumWidth = $largeWidth / $ratio;
					$smallWidth = $meddiumWidth / $ratio;
					$tinyWidth = $smallWidth / $ratio;
					if ($width < $largeWidth) {
						$largeWidth = $width;
						$meddiumWidth = null;
					} else if ($width < $meddiumWidth) {
						$largeWidth = $width;
						$meddiumWidth = null;
						$smallWidth = null;
					}
					$prefix = date("Ymd-His_");
					$extension = substr(strstr($type, "/"), 1);
					$filename = pathinfo($name)['filename'];
					$newName = $prefix . substr(md5(StringTool::removeAccentsAndSpaces($filename)), 0, 16);
					// large
					$contentUrl = $newImage->createNewImage($destination . $newName . '.' . $extension, $largeWidth);
					// meddium
					if ($meddiumWidth) $newImage->createNewImage($destination . $newName . '_m.' . $extension, (int) $meddiumWidth);
					// small
					$thumbnail = $smallWidth ? $newImage->createNewImage($destination . $newName . '_s.' . $extension, (int) $smallWidth) : $contentUrl;
					// tiny
					$newImage->createNewImage($destination . $newName . '_t.' . $extension, (int)$tinyWidth);
					$data[] = ['status' => 'success', 'data' => ['contentUrl'=>$contentUrl,'thumbnail'=>$thumbnail]];
				} else {
					$data[] = ['status' => 'error', 'message' => FileSystem::returnMessageError($error)];
				}
			}
			return ['status'=>'success', 'data'=>$data];
		} else {
			return ['status'=>'fail', 'data'=>$imagesUpload];
		}
	}

	/**
	 * @throws Exception
	 */
	protected function saveImageObject(array $data, ?array $params = []): array
	{
		$params = $params ?? [];
		$contentUrl = $data['contentUrl'] ?? null;
		$thumbnail = $data['thumbnail'] ?? null;
		$isPartOf = $params['isPartOf'] ?? null;
		unset($params['isPartOf']); // para não dar erro em chave estrangeira no insert creative work
		$image = new Image($contentUrl);
		$params['contentUrl'] = $image->getSrc();
		$params['contentSize'] = $image->getFileSize();
		$params['encodingFormat'] = $image->getEncodingFormat();
		$params['width'] = $image->getWidth();
		$params['height'] = $image->getHeight();
		$params['name'] = $params['name'] ?? $image->getBasename();
		$params['thumbnail'] = $thumbnail;
		// SAVE MEDIAOBJECT
		$dataImageObject = parent::createWithParent('mediaObject', $params);
		if (isset($dataImageObject['status']) && $dataImageObject['status'] == 'success' && $isPartOf) {
			$valueImage = $dataImageObject['data'][0];
			$idimageObject = $valueImage['idimageObject'];
			self::saveThingHasImageObject((int) $isPartOf, (int) $idimageObject, $params);
		}
		return $dataImageObject;
	}

	/**
	 * @param int $idthing
	 * @param int $idimageObject
	 * @param array $params
	 * @return string[]
	 */
	protected function saveThingHasImageObject(int $idthing, int $idimageObject, array $params): array
	{
		$maxPositionData = PDOConnect::run("select max(position) as maxpos from thing_has_imageObject where idthing='$idthing';");
		$maxpos = $maxPositionData[0]['maxpos'];
		$params['position'] = $maxpos === null ? 1 : $maxpos+1;
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
	protected function reorderingPosition($isPartOf): void
	{
		$connect = ApiFactory::request()->server()->connectBd('thing_has_imageObject');
		$newData = $connect->read(['where' => "`idthing`='$isPartOf'", "orderBy" => "position"]);
		foreach ($newData as $k => $v) {
			PDOConnect::crud()->setTable('thing_has_imageObject')->update(['position' => $k + 1], "`idimageObject`='{$v['idimageObject']}'");
		}
	}

	/**
	 * @param $idthing
	 * @return array
	 */
	protected function getHasPart($idthing): array
	{
		$data = PDOConnect::crud()->setTable('thing_has_imageObject')->read(['where'=>"`idthing`='$idthing'"]);
		$newData = [];
		foreach ($data as $value) {
			$newData[] = ApiFactory::request()->type('imageObject')->get(['idimageObject'=>$value['idimageObject']])->ready()[0];
		}
		return $newData;
	}
}
