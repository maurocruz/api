<?php
namespace Plinct\Api\Type;

use FilesystemIterator;
use Plinct\Api\Server\Entity;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ImageObject extends Entity implements TypeInterface {
    protected $table = "imageObject";
    protected $type = "ImageObject";
    protected $properties = [ "*" ];
    protected $hasTypes = [ "author" => "Person" ];

    public function put(array $params): array {
        unset($params['contentUrl']);
        return parent::put($params);
    }

    public function delete(array $params): array {
        $message = parent::delete([ "id" => $params['idIsPartOf'] ]);
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
}
