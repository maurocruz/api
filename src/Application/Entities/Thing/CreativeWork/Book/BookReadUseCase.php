<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\Book;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;

class BookReadUseCase extends CreativeWorkReadUseCase
{
	public function read(array $queryParams = []): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SupportApplication::sanitizeParams('Book', $queryParams);

		$getData = $this->databaseActions->read('book', $sanitizedParams);
		$getData->setJoin('creativeWork', 'creativeWork.thing = book.thing');
		return $getData->render();
	}
}
