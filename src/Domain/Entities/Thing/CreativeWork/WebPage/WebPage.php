<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\WebPage;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class WebPage extends CreativeWork
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	protected int $idwebPage;
	protected int $thing;
	public ?string $breadcrumb = null;
	public ?int $primaryImageOfPage = null;

	/**
	 * @param array $data
	 * @param bool $withParent
	 * @return array
	 */
	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data, $withParent);
	}
}
