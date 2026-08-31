<?php
namespace Plinct\Api\Application\Entities\Thing\Article;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;

class ArticleReadUseCase extends CreativeWorkReadUseCase
{
	public function read(array $sanitizedParams): array
	{
		$getData = $this->databaseActions->read('article', $sanitizedParams);
		$getData->setJoin('creativeWork', 'creativeWork.thing = article.thing');
		return $getData->render();
	}
}
