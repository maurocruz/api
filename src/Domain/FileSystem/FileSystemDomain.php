<?php
namespace Plinct\Api\Domain\FileSystem;

use Plinct\Api\Domain\Contracts\FileSystemInterface;
use Slim\Psr7\UploadedFile;

class FileSystemDomain implements FileSystemInterface
{
	public static function isFileUploaded(array $uploadFile): bool
	{
		foreach ($uploadFile as $value) {
			if (!($value instanceof UploadedFile)) return false;
		}
		return true;
	}
}
