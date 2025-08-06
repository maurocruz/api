<?php
namespace Plinct\Api\Request\Server;

use Exception;
use Imagick;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\ConnectBd;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Tool\Image\Image;

abstract class Entity implements HttpRequestInterface
{

	/**
	 * @var string
	 */
	protected string $table;
	/**
	 * @var array
	 */
	protected array $params = [];
  /**
   * @var string
   */
  protected string $type;
  /**
   * @var array
   */
  protected array $properties = [];
  /**
   * @var array
   */
  protected array $hasTypes = [];

	/**
	 * @param string $table
	 */
	public function setTable(string $table): void
	{
		$this->table = $table;
	}

	/**
	 * @param array $properties
	 */
	protected function setProperties(array $properties): void
	{
		$this->properties = $properties;
	}

	/**
	 * @return string
	 */
	public function getTable(): string {
		return $this->table;
	}

	/**
   * GET
   * @param array $params
   * @return array
   */
  public function get(array $params = []): array
  {
    $data = $this->getData($params);
		return self::sortData($data);
  }

	/**
	 * @param string $property
	 * @param array $params
	 * @return array|null
	 */
	protected function getProperties(string $property, array $params): ?array
	{
		$data = ApiFactory::request()->type($property)->get($params)->ready();
		return isset($data[0]) ? ApiFactory::response()->type($property)->setData($data)->ready() : null;
	}

	/**
	 * @param array $params
	 * @param ?string $joins
	 * @param string|null $where
	 * @return array
	 */
  protected function getData(array $params, ?string $joins = null, ?string $where = null): array
  {
    $data = new GetData($this->table);
		$data->setJoins($joins);
    $data->setParams($params);
		$data->setWhere($where);
	  return $data->render();
  }

	/**
	 * @param array $params
	 * @param array|null $uploadfiles
	 * @return array
	 */
  public function post(array $params, array $uploadfiles = null): array
  {
		$connect = new ConnectBd($this->table);
		$data = $connect->created($params);
	  if (empty($data)) {
			$idvalue = $connect->lastInsertId();
			$idname = "id".$this->table;
			$data = ApiFactory::request()->type($this->table)->get([$idname=>$idvalue])->ready();
			return ApiFactory::response()->message()->success("$this->table successfully created", $data);
	  } else {
		  return ApiFactory::response()->message()->fail()->generic($data);
	  }
  }

	/**
	 * @param string $parentName
	 * @param array|null $params
	 * @param array|null $uploadedFiles
	 * @return array
	 */
	protected function createWithParent(string $parentName, array $params = null, array $uploadedFiles = null): array
	{
		$params['type'] = $params['type'] ?? ucfirst($this->table);
		// SAVE PARENT
		PDOConnect::run("START TRANSACTION");
		$dataParent = ApiFactory::request()->type($parentName)->httpRequest()->setPermission()->post($params, $uploadedFiles);
		if (isset($dataParent['status']) && $dataParent['status'] === 'success') {
			$value = $dataParent['data'][0];
			foreach ($value as $key => $val) {
				if (str_starts_with($key, 'id')) {
					$params[substr($key,2)] = $val;
				}
			}
			// SAVE CHILD
			$data = self::post($params);
			if (isset($data['status']) && $data['status'] == 'success') {
				PDOConnect::run("COMMIT;");
			} else {
				PDOConnect::run("ROLLBACK;");
			}
			return $data;
		}
		return ApiFactory::response()->message()->fail()->generic($dataParent);
	}

	/**
	 * PUT
	 * @param array|null $params
	 * @return array
	 */
  public function put(array $params = null): array
  {
		// CONNECT
	  $connect = new ConnectBd($this->table);
		$data = $connect->update($params);
		if ($data['status'] === 'success') {
			$idname = "id$this->table";
			$idvalue = $params[$idname] ?? null;
			if ($idvalue) {
				$getData = new GetData($this->table, false);
				$rowUpdated = $getData->setParams([$idname => $idvalue])->render();
				$data['data'] = $rowUpdated;
			}
		}
		return $data;
  }

	/**
	 * @param string $parentName
	 * @param array|null $params
	 * @return array
	 */
	protected function update(string $parentName, array $params = null): array
	{
		$idchildName = 'id'.$this->table;
		$idchildValue = $params[$idchildName] ?? null;
		if ($idchildValue) {
			$getData = new GetData($this->table, false);
			$dataChild = $getData->setParams([$idchildName=>$idchildValue])->render();
			if (!empty($dataChild)) {
				$putChild = self::put($params);
				if ($putChild['status'] === 'success') {
					$idparent = $putChild['data'][0][$parentName];
					$putParent = ApiFactory::request()->type($parentName)->put(['id'.$parentName=>$idparent] + $params)->ready();
					if ($putParent['status'] === 'success') {
						return ApiFactory::response()->message()->success("$this->table was updated", [$putChild, $putParent]);
					} else {
						return ApiFactory::response()->message()->error()->anErrorHasOcurred($putParent);
					}
				} else {
					return ApiFactory::response()->message()->error()->anErrorHasOcurred($putChild);
				}
			} else {
				return ApiFactory::response()->message()->fail()->returnIsEmpty();
			}
		}
		return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: $idchildName"]);
	}

  /**
   * DELETE
   * @param array $params
   * @return array
   */
  public function delete(array $params): array
  {
		$limit = $params['limit'] ?? '1';
		unset($params['limit']);
		$connect = new ConnectBd($this->table);
		return $connect->delete($params, $limit);
  }

	/**
	 * @param string $parentName
	 * @param array|null $params
	 * @return array
	 */
	protected function erase(string $parentName, array $params = null): array
	{
		$idchildName = 'id'.$this->table;
		$idchildValue = $params[$idchildName] ?? $params[$this->table] ?? null;
		if ($idchildValue) {
			$dataChild = self::getData([$idchildName=>$idchildValue]);
			if (!empty($dataChild)) {
				$idparent = $dataChild[0][$parentName];
				return ApiFactory::request()->type($parentName)->delete(['id'.$parentName=>$idparent])->ready();
			}else {
				return ApiFactory::response()->message()->fail()->generic($params, ucfirst($this->table).' id not found');
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: $idchildName or $this->table"]);
		}
	}

	/**
	 * @param array $array
	 * @return array
	 */
	protected function sortData(array $array): array
	{
		$new_array = array();
		foreach (array_values($array) as $key => $value) {
			ksort($value);
			$new_array[$key] = $value;
		}
		return $new_array;
	}

	/**
	 * @param string $idHasPart
	 * @param string $typeHasPart
	 * @param string $idIsPartOf
	 * @param string $typeIsPartOf
	 * @return array
	 */
	protected function createRelationShip(string $idHasPart, string $typeHasPart, string $idIsPartOf, string $typeIsPartOf): array
	{
		return (new Relationship())->createRelationShip($idHasPart, $typeHasPart, $idIsPartOf, $typeIsPartOf);
	}

	/**
	 * @param string $idHasPart
	 * @param string $typeHasPart
	 * @return array
	 */
	protected function getHasPart(string $idHasPart, string $typeHasPart): array
	{
		return (new Relationship())->getHasPart(['idHasPart'=>$idHasPart, 'typeHasPart'=>$typeHasPart]);
	}

	/**
	 * @param string|null $properties
	 * @return string[]|null
	 */
	protected static function propertiesToArray(string $properties = null): ?array
	{
		if (!$properties) return [];
		$propertiesArray = explode(',',$properties);
		array_walk($propertiesArray, function (&$value) {$value = trim($value);});
		return $propertiesArray;
	}

	/**
	 * @throws Exception
	 */
	protected function uploadfiles(array $params, array $uploadfiles): array
	{
		$filesUpload = $uploadfiles['uploadfile'] ?? null;
		$typeHasPart = $params['typeHasPart'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		$location = $params['location'] ?? $params['destination'] ?? "";
		if ($filesUpload) {
			$filesUploadError = $filesUpload['error'] ?? null;
			$numberOfFiles = count($filesUpload['name']);
			foreach ($filesUploadError as $key => $error) {
				if ($error == UPLOAD_ERR_OK) {
					$name = $filesUpload['name'][$key];
					$type = $filesUpload['type'][$key];
					$groupType = strstr($type, '/', true);
					$tmpName = $filesUpload['tmp_name'][$key];
					$size = $filesUpload['size'][$key];
					if (is_uploaded_file($tmpName)) {
						// SET FOLDER DESTINATION
						$folder = "/public/uploads/$groupType" . (str_starts_with($location, '/') ? "$location/" : "/$location/") ;
						$pathfile = $_SERVER['DOCUMENT_ROOT'] . $folder;
						if (is_dir($pathfile) === false) {
							mkdir($pathfile, 0777, true);
						}
						// NEW NAME
						$prefix = date("Ymd-His_");
						$pathinfo = pathinfo($name);
						$extension = $pathinfo['extension'];
						$filename = $pathinfo['filename'];
						$newName = $prefix . md5($filename) . '.' . $extension;
						$thumbName = $prefix . md5($filename) . '_thumb.jpeg';
						$host = ApiFactory::request()->configuration()->getHost();
						// PARSER META DATA
						$parser = ApiFactory::helper()->ParserMidia($tmpName, $type);
						$params['author'] = $parser->getAuthor();
						$params['bitrate'] = $parser->getBitrate();
						$params['contentSize'] = $size;
						$params['contentUrl'] = $host . $folder . $newName;
						$params['dateModified'] = $parser->getDateModified() ?? $params['dateModified'] ?? date('Y-m-d H:i:s');
						$params['datePublished'] = $parser->getDatePublished();
						$params['duration'] = $parser->getDuration();
						$params['encodingFormat'] = $parser->getEncodingFormat();
						$params['height'] = $parser->getHeight();
						$params['name'] = isset($params['name']) && $numberOfFiles === 1 ? $params['name'] : $parser->getHeadLine() ?? $newName;
						$params['headline'] = $params['name'];
						$params['publisher'] = $parser->getPublisher();
						$params['uploadDate'] = date('Y-m-d H:i:s');
						$params['width'] = $parser->getWidth();
						$params['url'] = $params['url'] ?? $params['contentUrl'];
						// UPLOAD FILE OR SAVE IMAGE
						if ($groupType == 'image') {
							if (str_contains($type,"svg")) {
								$uploadedReturn = move_uploaded_file($tmpName, $pathfile.$newName);
							} else {
								$uploadedReturn = self::uploadImage($newName, $tmpName, $folder);
							}
						} elseif ($groupType == 'application') {
							$uploadedReturn = move_uploaded_file($tmpName, $pathfile.$newName);
						} else {
							return ApiFactory::response()->message()->fail()->generic(['message'=>'encodingFormat not recognized']);
						}
						// CREATE ITEM AND IS PART OF
						if ($uploadedReturn) {
							// CREATE THUMBNAIL
							if (isset($uploadedReturn['status']) && $uploadedReturn['status'] === 'success' && isset($uploadedReturn['data']['thumbnail'])) {
								$params['thumbnail'] = $uploadedReturn['data']['thumbnail'];
							} elseif ($params['encodingFormat'] == 'application/pdf') {
								$imagick = new Imagick();
								$imagick->readImage($pathfile . $newName.'[0]');
								$imagick->setImageFormat('jpeg');
								$imagick->setResolution(300, 300);
								$imagick->writeImage($pathfile . $thumbName);
								$params['thumbnail'] = $host . $folder . $thumbName;
							} else {
								// TODO fazer thumbnail com FFMpeg no plinct
								$params['thumbnail'] = "";
							}
							$params['type'] = match ($groupType) {
								"video" => "VideoObject",
								"audio" => "AudioObject",
								"image" => "ImageObject",
								default => "MediaObject",
							};
							$parentType = $params['type'] == "MediaObject" ? 'creativeWork' : 'mediaObject';
							$this->table = lcfirst($params['type']);
							$dataCreated = self::createWithParent($parentType, $params);
							if (isset($dataCreated['status']) && $dataCreated['status'] === 'success') {
								if($typeHasPart && $idHasPart) {
									$item = $dataCreated['data'][0];
									$idIsPartOf = $item['idthing'];
									$typeIsPartOf = $item['type'];
									$this->table = "thing_has_thing";
									$dataHasThing = self::createRelationShip($idHasPart, ucfirst($typeHasPart), $idIsPartOf, ucfirst($typeIsPartOf));
									$dataCreated['data'][] = $dataHasThing;
								}
								return $dataCreated;
							} else {
								return ApiFactory::response()->message()->fail()->generic(['message'=>'an error has ocurred']);
							}
						}
					}
				}
			}
		}
		return ApiFactory::response()->message()->fail()->generic(['message'=>'no uploaded files']);
	}

	/**
	 * @throws Exception
	 */
	public function uploadImage($newName, $tmp_name, $destination, $largeWidth = 1280): array
	{
		$newImage = new Image($tmp_name);
		$width = $newImage->getWidth();
		$ratio = 1.618; // number gold
		$meddiumWidth = round($largeWidth / $ratio); // 791
		$smallWidth = round($meddiumWidth / $ratio); // 489
		$tinyWidth = round($smallWidth / $ratio); // 302
		if ($width < $largeWidth) {
			$largeWidth = $width;
			$meddiumWidth = null;
		} else if ($width < $meddiumWidth) {
			$largeWidth = $width;
			$meddiumWidth = null;
			$smallWidth = null;
		}
		$pathinfo = pathinfo($destination.$newName);
		$dirname = $pathinfo['dirname'];
		$filename = $pathinfo['filename'];
		$extension = $pathinfo['extension'];

		$largeFileName = $dirname.'/'.$filename.'.'.$extension;
		$meddiumFileName = $dirname.'/'.$filename.'_m.'.$extension;
		$smallFileName = $dirname.'/'.$filename.'_s.'.$extension;
		$tinyFileName = $dirname.'/'.$filename.'_t.'.$extension;
		// large
		$contentUrl = $newImage->createNewImage($largeFileName, $largeWidth);
		// meddium
		if ($meddiumWidth) $newImage->createNewImage($meddiumFileName, (int) $meddiumWidth);
		// small
		$thumbnail = $smallWidth ? $newImage->createNewImage($smallFileName, (int) $smallWidth) : $contentUrl;
		// tiny
		$newImage->createNewImage($tinyFileName, (int)$tinyWidth);
		// RETURN
		if (empty($newImage->getError())) {
			return ['status' => 'success', 'data' => ['contentUrl'=>$contentUrl,'thumbnail'=>$thumbnail]];
		} else {
			return ['status' => 'error', 'data' => $newImage->getError()];
		}
	}
}
