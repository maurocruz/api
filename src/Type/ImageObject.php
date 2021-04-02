<?php
namespace Plinct\Api\Type;

use FilesystemIterator;
use Plinct\Api\Server\Entity;
use Plinct\Tool\Image;
use Plinct\Tool\Thumbnail;
use Plinct\Tool\StringTool;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ImageObject extends Entity implements TypeInterface {
    protected $table = "imageObject";
    protected $type = "ImageObject";
    protected $properties = [ "contentUrl", "caption", "keywords", "representativeOfPage", "position", "width", "height", "href", "license", "acquireLicensePage" ];
    protected $hasTypes = [ "author" => "Person" ];

    public function get(array $params): array {
        $dataThumb = null;
        $dataSizes = null;
        $postParams = null;
        $data = parent::getData($params);
        // IF NOT EXISTS THUMBNAIL AND SIZES
        if (!empty($data)) {
            foreach ($data as $value) {
                if (!$value['thumbnail'] || !$value['width'] || !$value['height'] || !$value['contentSize'] || !$value['encodingFormat']) {
                    $value['id'] = $value['idimageObject'];
                    $IMAGE = !$value['thumbnail'] ? new Thumbnail($value['contentUrl']) : new Image($value['contentUrl']);
                    // THUMBNAIL
                    if (!$value['thumbnail']) $value['thumbnail'] = $IMAGE->getThumbnail(200);
                    // WIDTH
                    if (!$value['width']) $value['width'] = $IMAGE->getWidth();
                    // HEIGHT
                    if (!$value['height']) $value['height'] = $IMAGE->getHeight();
                    // CONTENT SIZE
                    if (!$value['contentSize']) $value['contentSize'] = $IMAGE->getFileSize();
                    // ENCONDING FORMAT
                    if (!$value['encodingFormat']) $value['encodingFormat'] = $IMAGE->getMimeType();
                    $this->put($value);
                }
                $data[] = $value;
            }
        }
        return parent::buildSchema($params, $data);
    }

    public function post(array $params): array {
        $imagesUploaded = null;
        $newParams = null;
        // upload image
        if (isset($_FILES['imageupload'])) {
            foreach ($_FILES['imageupload'] as $keyImagesUploaded => $valueImagesUploaded) {
                foreach ($valueImagesUploaded as  $keyImage => $valueImage) {
                    $imagesUploaded[$keyImage][$keyImagesUploaded] = $valueImage;
                }
            }
            foreach ($imagesUploaded as $keyForUpload => $valueForUpload) {
                if ($valueForUpload['size'] > 0) {
                    $params['contentUrl'] = $params['location'] . DIRECTORY_SEPARATOR . self::uploadImage($valueForUpload, $params['location']);
                    $newParams[] = $params;
                }
            }
            foreach ($newParams as $valueParams) {
                unset($valueParams['location']);
                $this->table = "imageObject";
                parent::post($valueParams);
            }
            return [ "message" => "images uploaded" ];
        } elseif (isset($params['id']) && is_array($params['id'])) {
            foreach ($params['id'] as $valueParams) {
                if (isset($params['tableHasPart'])) {
                    $newParams['tableHasPart'] = $params['tableHasPart'];
                    $newParams['idHasPart'] = $params['idHasPart'];
                    $newParams['id'] = $valueParams;
                }
                $this->table = "imageObject";
                parent::post($newParams);
            }
            return [ "message" => "images uploaded" ];
        } else {
            return parent::post($params);
        }
    }

    public function delete(array $params): array {
        $message = parent::delete($params);
        $imageFile = isset($params['contentUrl']) ? realpath($_SERVER['DOCUMENT_ROOT'].$params['contentUrl']) : null;
        if ($imageFile && $message["message"] == "Deleted successfully" && file_exists($imageFile)) {
            unlink($imageFile);
            $this->deleteThumbs($imageFile);
        }
        return $message;
    }

    // unlike thumbs folder
    private function deleteThumbs($file) {
        $folderThumbs = dirname($file)."/thumbs";
        if (is_dir($folderThumbs)) {
            $directory = new RecursiveDirectoryIterator($folderThumbs, FilesystemIterator::SKIP_DOTS);
            $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($iterator as $file) {
                $file->isDir() ? rmdir($file) : unlink($file);
            }
        }
    }

    public function createSqlTable($type = null): array {
        $message[] =  parent::createSqlTable("ImageObject");
        return $message;
    }    
    
    public static function getRepresentativeImageOfPage($data, $mode = "string") {
        if ($data) {
            foreach ($data as $valueImage) {
                if (isset($valueImage['representativeOfPage']) && $valueImage['representativeOfPage'] == true) {
                    return $mode == "string" ? $valueImage['contentUrl'] : $valueImage;
                }
            }
            return $mode == "string" ? $data[0]['contentUrl'] : $data[0];
        }
        return null;
    }
    
    private static function uploadImage($imageUpload, $location): string {
        $dir = substr($location, 0, 1) == '/' ? $location : '/'.$location;
        $filename = StringTool::removeAccentsAndSpaces($imageUpload['name']);
        $path = $dir."/".$filename;
        (new Thumbnail($imageUpload['tmp_name']))->uploadImage($path);
        return $filename;
    }  
}
