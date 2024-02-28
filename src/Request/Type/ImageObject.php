<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\Relationship\Relationship;
use Plinct\Api\Server\Maintenance;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\FileSystem\FileSystem;
use Plinct\Tool\Image\Image;
use ReflectionException;

class ImageObject extends Entity
{
  /**
   * @var string
   */
  protected string $table = "imageObject";
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

  /**
   * @param array $params
   * @return array
   * @throws Exception
   */
  public function get(array $params = []): array
  {
		// IMAGES IS PART OF
    if (isset($params['isPartOf'])) Return $this->getImageIsPartOf($params['isPartOf']);

    // vars
    $thumbnail = $params['thumbnail'] ?? null;
    $format = $params['format'] ?? null;
    if ($thumbnail == "on") $params['properties'] = "*";
    unset($params['thumbnail']);

    // GET
    $data = parent::get($params);

    // THUMBNAIL ON
    if ($thumbnail=='on') {
      $itemList = $data['itemListElement'] ?? $data;

      foreach ($itemList as $key => $value) {
        $item = $format ? $value['item'] : $value;

        if (!$item['thumbnail']) {
          $image = new Image($item['contentUrl']);
          $image->thumbnail("200");
          $contentSize = $image->getFileSize();
          $width = $image->getWidth();
          $height = $image->getHeight();
          $encodingFormat = $image->getEncodingFormat();
          $thumbnailData = $image->getThumbSrc();
          $data['itemListElement'][$key]['item']['contentSize'] = $contentSize;
          $data['itemListElement'][$key]['item']['width'] = $width;
          $data['itemListElement'][$key]['item']['height'] = $height;
          $data['itemListElement'][$key]['item']['encodingFormat'] = $encodingFormat;
          $data['itemListElement'][$key]['item']['thumbnail'] = $thumbnailData;
          // save data
          $id = ArrayTool::searchByValue($item['identifier'], "id")['value'];
          $newParams = [ "id" => $id, "contentSize" => $contentSize, "width" => $width, "height" => $height, "encodingFormat" => $encodingFormat, "thumbnail" => $thumbnailData ];
          parent::put($newParams);
        }
      }
    }

    return $data;
  }

	/**
	 * @throws Exception
	 */
	public function post(array $params = null, array $uploadedFiles = null): array
	{
		$returns = [];
		$tableHasPart = $params['tableHasPart'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		$position = $params['position'] ?? 1;
		$representativeOfPage = $params['representativeOfPage'] ?? null;
		$caption = $params['caption'] ?? null;
		$destination = $params['pathDestinations'] ?? $params['location'] ?? $params['imageFolder'] ?? null;
		unset($params['tableHasPart'], $params['idHasPart'], $params['pathDestinations'], $params['location'], $params['imageFolder']);

		// UPLOAD FILES
		if (isset($uploadedFiles['imageupload'])) {
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
			$uploadedFilesReturns = $fileSystem->uploadFiles($uploadedFiles['imageupload']);

			foreach ($uploadedFilesReturns as $fileUploaded) {
				if ($fileUploaded['status']) {
					$imageSrc = str_replace($_SERVER['DOCUMENT_ROOT'], '', $fileUploaded['data']);
					$image = new Image($imageSrc);
					$image->thumbnail(200);
					$imageParams['contentUrl'] = $image->getSrc();
					$imageParams['contentSize'] = $image->getFileSize();
					$imageParams['thumbnail'] = $image->getThumbSrc();
					$imageParams['width'] = $image->getWidth();
					$imageParams['height'] = $image->getHeight();
					$imageParams['encodingFormat'] = $image->getEncodingFormat();
					$newParams = array_merge($params, $imageParams);
					$idIsPartOf = parent::post($newParams)['id'];

					// ADDED ID IMAGEOBJECT IN RELATIONSHIP TABLE
					if($tableHasPart && $idHasPart && $idIsPartOf) {
						ApiFactory::server()->relationship($tableHasPart, (int) $idHasPart, 'imageObject', $idIsPartOf)->post(['position' => $position, 'representativeOfPage' => $representativeOfPage, 'caption' => $caption]);
						PDOConnect::run("UPDATE {$tableHasPart}_has_imageObject SET position=position+1 WHERE `id$tableHasPart`=$idHasPart AND `idimageObject`!=$idIsPartOf");
					}
					$returns[] = array_merge(['idimageObject'=>$idIsPartOf], $newParams);
				}
			}
		} else {
			$idIsPartOf = $params['idIsPartOf'];
			if($tableHasPart && $idHasPart && $idIsPartOf) {
				$returnRel = ApiFactory::server()->relationship($tableHasPart, $idHasPart, 'imageObject', $idIsPartOf)->post(['position' => $position, 'representativeOfPage' => $representativeOfPage, 'caption' => $caption]);
				$returnUpdate = PDOConnect::run("UPDATE {$tableHasPart}_has_imageObject SET position=position+1 WHERE `id$tableHasPart`=$idHasPart AND `idimageObject`!=$idIsPartOf");
			}
			if (empty($returnRel) && empty($returnUpdate)) {
				$returns = ["status"=>"ok","message"=>"relationship added"];
			}
		}
		// SUCCESS
		if(!empty($returns))
			return ApiFactory::response()->message()->success()->success("Files uploaded!", $returns);
		// FAIL
		return ApiFactory::response()->message()->fail()->inputDataIsMissing();
	}

  /**
   * @param ?array $params
   * @return array
   */
  public function put(array $params = null): array
  {
    unset($params['contentUrl']);
    return parent::put($params);
  }

	public function delete(array $params): array
	{
		$tableHasPart = isset($params['tableHasPart']) ? lcfirst($params['tableHasPart']) : null;
		$idHasPart = $params['idHasPart'] ?? null;
		$tableIsPartOf = 'imageObject';
		$idIsPartOf = $params['idIsPartOf'] ?? null;
		if ($tableHasPart && $idHasPart && $idIsPartOf) {
			$relationship = new Relationship($tableHasPart, $idHasPart, $tableIsPartOf, $idIsPartOf);
			$relationshipTable = $tableHasPart . '_has_' . $tableIsPartOf;
			$relationshipRow = PDOConnect::run("SELECT position FROM $relationshipTable WHERE id$tableHasPart=$idHasPart AND id$tableIsPartOf=$idIsPartOf");
			if(empty($relationshipRow)) {
				return ApiFactory::response()->message()->fail()->generic($relationshipRow, 'item not found!');
			} elseif(isset($relationshipRow['error'])) {
				return $relationshipRow;
			}
			$position = $relationshipRow[0]['position'];
			PDOConnect::run("UPDATE $relationshipTable SET position=position-1 WHERE id$tableHasPart=$idHasPart AND position>$position");
			return $relationship->delete();
		}
		return parent::delete($params);
	}

	/**
   * @param null $type
   * @return array
   * @throws ReflectionException
   */
  public function createSqlTable($type = null): array
  {
    $message[] =  parent::createSqlTable("ImageObject");
    return $message;
  }

  /**
   * @param $data
   * @param string $mode
   * @return null
   */
  public static function getRepresentativeImageOfPage($data, string $mode = "string")
  {
    if ($data) {
      foreach ($data as $valueImage) {
        if (isset($valueImage['representativeOfPage']) && $valueImage['representativeOfPage'] === true) {
          return $mode == "string" ? $valueImage['contentUrl'] : $valueImage;
        }
      }
      return $mode == "string" ? $data[0]['contentUrl'] : $data[0];
    }
    return null;
  }

	/**
	 * @param $idIsPartOf
	 * @return array
	 */
	private function getImageIsPartOf($idIsPartOf): array
	{
		$returns = [
			'@context' => "https://plinct.com.br/isPartOf",
			'@type' => 'ImageObject',
			'@id' => $idIsPartOf,
			"isPartOf" => []
		];

		$tablesHasPart = Maintenance::setTableHasImageObject();

		foreach ($tablesHasPart as $value) {
			$relationshipTable = $value['tableName'];
			$tableHasPart = strstr($value['tableName'], "_", true);
			$contextArray = [
				"@context" => "https://schema.org",
				"@type" => ucfirst($tableHasPart)
			];

			if ($tableHasPart !== 'group') {
				$query = "SELECT `$tableHasPart`.* FROM `$relationshipTable`, `$tableHasPart` WHERE `idimageObject`=$idIsPartOf AND $tableHasPart.id$tableHasPart=$relationshipTable.id$tableHasPart;";
				$data = PDOConnect::run($query);

				if (!isset($data['error']) && count($data) > 0) {
					foreach ($data as $valueTableIspartOf) {
						$returns['isPartOf'][] = array_merge($contextArray, $valueTableIspartOf);
					}
				}
			}
		}

		return $returns;
	}
}
