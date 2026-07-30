<?php
namespace Plinct\Api\Domain\Contracts;

interface FileSystemInterface
{
	public static function isFileUploaded(array $uploadFile): bool;
}
