<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\Book;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;

class BookReadUseCase extends CreativeWorkReadUseCase
{
	public function read(array $sanitizedParams): array
	{
		$getData = $this->databaseActions->read('book', $sanitizedParams);
		$getData->setJoin('creativeWork', 'creativeWork.thing = book.thing');
		return $getData->render();
	}
}
