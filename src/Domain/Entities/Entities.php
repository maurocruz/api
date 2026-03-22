<?php
namespace Plinct\Api\Domain\Entities;

use Plinct\Api\Domain\Contracts\EntityInterface;
use Plinct\Api\Domain\Entities\Thing\Action\Action;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\Article\Article;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\Book\Book;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\ImageObject\ImageObject;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\MediaObject;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\WebPage\WebPage;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\WebPageElement\WebPageElement;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\WebSite\WebSite;
use Plinct\Api\Domain\Entities\Thing\Product\Product;
use Plinct\Api\Domain\Entities\Thing\Thing;
use Plinct\Api\Domain\Modules\ModuleDomain;
use ReflectionClass;
use ReflectionException;

class Entities
{
	public const array ENTITIES = [
		['name' => 'Action', 'object' => Action::class],
		['name' => 'Article', 'object' => Article::class],
		['name' => 'Book', 'object' => Book::class ],
		['name' => 'CreativeWork', 'object' => CreativeWork::class],
		['name' => 'ImageObject', 'object' => ImageObject::class],
		['name' => 'MediaObject', 'object' => MediaObject::class],
		['name' => 'Product', 'object' => Product::class ],
		['name' => 'Thing', 'object' => Thing::class],
		['name' => 'WebPage', 'object' => WebPage::class],
		['name' => 'WebPageElement', 'object' => WebPageElement::class],
		['name' => 'WebSite', 'object' => WebSite::class]
	];

	public static function hasEntity(string $entityName): bool
	{
		foreach (self::ENTITIES as $entity) {
			if ($entity['name'] === ucfirst($entityName)) return true;
		}
		return false;
	}

	public static function getEntity(string $entityName): ?EntityInterface
	{
		foreach (self::ENTITIES as $entity) {
			if ($entity['name'] === ucfirst($entityName)) return new $entity['object']();
		}
		return null;
	}

	public static function getEntityNamespace(string $entityName): ?string
	{
		foreach (self::ENTITIES as $entity) {
			if ($entity['name'] === ucfirst($entityName)) return $entity['object'] ?? null;
		}
		return null;
	}

	public static function isEnabled(string $entityName): bool
	{
		return ModuleDomain::moduleIsEnabled($entityName);
	}

	/**
	 * @throws ReflectionException
	 */
	public static function extractIdentifiers(string $entityName, array $itemData): array
	{
		$items = [];
		$identifiers = [];
		$itemDataFiltered = array_filter($itemData);
		$entityClassName = self::getEntityNamespace($entityName);

		$reflection = new ReflectionClass($entityClassName);

		foreach ($reflection->getProperties() as $property) {
			// EXTRACT ITEMS
			if ($property->isPublic() && array_key_exists($property->getName(), $itemDataFiltered)) {
				$items[$property->getName()] = $itemDataFiltered[$property->getName()];
			}
			// EXTRACT IDENTIFIERS
			if ($property->isProtected() && array_key_exists($property->getName(), $itemDataFiltered)) {
				$identifiers[$property->getName()] = $itemDataFiltered[$property->getName()];
			}
		}
		return ['items' => $items, 'identifiers' => $identifiers];
	}
}
