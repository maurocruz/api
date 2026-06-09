<?php
namespace Plinct\Api\Domain\Entities;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Contracts\EntityInterface;
use Plinct\Api\Domain\Entities\Thing\Action\Action;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\Article\Article;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\Book\Book;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\Certification\Certification;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\Collection\Collection;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\AudioObject\AudioObject;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\ImageObject\ImageObject;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\MediaObject;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\VideoObject\VideoObject;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\Review\Review;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\WebPage\WebPage;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\WebPageElement\WebPageElement;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\WebSite\WebSite;
use Plinct\Api\Domain\Entities\Thing\Event\Event;
use Plinct\Api\Domain\Entities\Thing\Intangible\Invoice\Invoice;
use Plinct\Api\Domain\Entities\Thing\Intangible\Offer\Offer;
use Plinct\Api\Domain\Entities\Thing\Intangible\Order\Order;
use Plinct\Api\Domain\Entities\Thing\Intangible\Property;
use Plinct\Api\Domain\Entities\Thing\Intangible\Service\Service;
use Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue\ContactPoint;
use Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue\GeoCoordinates;
use Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue\OrderItem;
use Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue\PostalAddress;
use Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue\PropertyValue;
use Plinct\Api\Domain\Entities\Thing\Intangible\Trip\Trip;
use Plinct\Api\Domain\Entities\Thing\Organization\Organization;
use Plinct\Api\Domain\Entities\Thing\Person\Person;
use Plinct\Api\Domain\Entities\Thing\Place\Place;
use Plinct\Api\Domain\Entities\Thing\Product\Product;
use Plinct\Api\Domain\Entities\Thing\Thing;
use Plinct\Api\Domain\Entities\User\User;
use Plinct\Api\Domain\Entities\User\User_history;
use Plinct\Api\Domain\Entities\User\User_passwordReset;
use Plinct\Api\Domain\Entities\User\User_privileges;
use Plinct\Api\Domain\Modules\ModuleDomain;
use ReflectionClass;
use ReflectionException;

class Entities
{
	public const array ENTITIES = [
		['name' => 'Action', 'object' => Action::class],
		['name' => 'Article', 'object' => Article::class],
		['name' => 'AudioObject', 'object' => AudioObject::class],
		['name' => 'Book', 'object' => Book::class ],
		['name' => 'Certification', 'object' => Certification::class ],
		['name' => 'Collection', 'object' => Collection::class ],
		['name' => 'ContactPoint', 'object' => ContactPoint::class ],
		['name' => 'CreativeWork', 'object' => CreativeWork::class],
		['name' => 'Event', 'object' => Event::class],
		['name' => 'GeoCoordinates', 'object' => GeoCoordinates::class],
		['name' => 'ImageObject', 'object' => ImageObject::class],
		['name' => 'Invoice', 'object' => Invoice::class],
		['name' => 'MediaObject', 'object' => MediaObject::class],
		['name' => 'Order', 'object' => Order::class],
		['name' => 'OrderItem', 'object' => OrderItem::class],
		['name' => 'Offer', 'object' => Offer::class],
		['name' => 'Organization', 'object' => Organization::class ],
		['name' => 'Person', 'object' => Person::class],
		['name' => 'Place', 'object' => Place::class],
		['name' => 'PostalAddress', 'object' => PostalAddress::class],
		['name' => 'Product', 'object' => Product::class ],
		['name' => 'PropertyValue', 'object' => PropertyValue::class],
		['name' => 'Review', 'object' => Review::class],
		['name' => 'Thing', 'object' => Thing::class],
		['name' => 'Thing_has_thing', 'object' => Property::class],
		['name' => 'Trip', 'object' => Trip::class],
		['name' => 'Service', 'object' => Service::class],
		['name' => 'User', 'object' => User::class],
		['name' => 'User_history', 'object' => User_history::class],
		['name' => 'User_passwordReset', 'object' => User_passwordReset::class],
		['name' => 'User_privileges', 'object' => User_privileges::class],
		['name' => 'VideoObject', 'object' => VideoObject::class],
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
	public static function isOwnProperty(string $entityName, string $property): bool
	{
		$entityReflection = new EntityReflections($entityName);
		$properties = $entityReflection->getOwnProperties();
		foreach ($properties as $propertyReflection) {
			if ($property == $propertyReflection->getName()) {
				return true;
			}
		}
		return false;
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
