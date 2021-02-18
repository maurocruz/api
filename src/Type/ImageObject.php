<?php

namespace Plinct\Api\Type;

use FilesystemIterator;
use Plinct\Api\Server\Entity;
use Plinct\Tool\Thumbnail;
use Plinct\Tool\StringTool;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ImageObject extends Entity implements TypeInterface
{
    protected $table = "imageObject";
    protected $type = "ImageObject";
    protected $properties = [ "contentUrl", "caption", "keywords", "representativeOfPage", "position", "width", "height", "href", "license", "acquireLicensePage" ];
    protected $hasTypes = [ "author" => "Person" ];

    public function get(array $params): array {
        $dataThumb = null;
        $dataSizes = null;
        $data = parent::getData($params);
        // EXTRA PROPERTIES
        if (!empty($data) && isset($params['properties']) && !array_key_exists('error', $data)) {
            // THUMBNAIL
            if (strpos($params['properties'], "thumbnail") !== false) {
                foreach ($data as $valueThumb) { 
                    if (file_exists($_SERVER['DOCUMENT_ROOT'].$valueThumb['contentUrl'])) {
                        $thumbnail = new Thumbnail($valueThumb['contentUrl']);
                        $valueThumb['thumbnail'] = $thumbnail->getThumbnail(200);

                        $dataThumb[] = $valueThumb;
                    }
                }
                $data = $dataThumb;
            }
            // SIZES
            if (strpos($params['properties'], 'sizes') !== false) {
                foreach ($data as $valueSizes) {
                    $imagePath = $_SERVER['DOCUMENT_ROOT'].$valueSizes['contentUrl'];
                    if (file_exists($imagePath) && is_file($imagePath)) {
                        list( $width, $height) = getimagesize($imagePath);
                        $valueSizes['width'] = $width;
                        $valueSizes['height'] = $height;
                        $dataSizes[] = $valueSizes;
                    }
                }
                $data = $dataSizes;
            }
        }
        return parent::buildSchema($params, $data);
    }

    public function post(array $params): array {
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
            return [ "messagem" => "images uploaded" ];
        } elseif(isset($params['id']) && is_array($params['id'])) {
            foreach ($params['id'] as $valueParams) {
                if (isset($params['tableHasPart'])) {
                    $newParams['tableHasPart'] = $params['tableHasPart'];
                    $newParams['idHasPart'] = $params['idHasPart'];
                    $newParams['id'] = $valueParams;
                }
                $this->table = "imageObject";
                parent::post($newParams);
            }
            return [ "messagem" => "images uploaded" ];
        } else {
            return parent::post($params);
        }
    }

    public function put(array $params = null): array {
        return parent::put($params);
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
