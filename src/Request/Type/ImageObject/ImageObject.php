<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\ImageObject;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Relationship\Relationship;
use Plinct\Api\Server\Maintenance;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\Image\Image;

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
				$pathfile = str_replace($_SERVER['DOCUMENT_ROOT'], '', $fileUploaded['data']);
				// SAVE NEW IMAGE OBJECT
				$saveImageObject = parent::saveImageObject($pathfile, $params);
				if (isset($saveImageObject['id'])) {
					if ($idHasPart) {
						$idIsPartOf = $saveImageObject['id'];
						parent::saveThingHasImageObject((int) $idHasPart, (int) $idIsPartOf, $params);
					}
					$returns[] = ApiFactory::response()->message()->success("File uploaded", ['contentUrl'=>$pathfile]);
				} else {
					$returns[] = ApiFactory::response()->message()->fail()->generic($saveImageObject);
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
