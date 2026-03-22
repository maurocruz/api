<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\WebPage;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkCreateUseCase;
use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

class WebPageCreateUseCase extends CreativeWorkCreateUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function create(array $parseBody) : array
	{
		$parseBody['type'] = $parseBody['type'] ?? 'WebPage';
		$isPartOf = $parseBody['isPartOf'] ?? null;
		if (!$isPartOf) {
			return ['status'=> false, 'message'=> 'isPartOf is required'];
		}

		// SANITIZE PARAMS
		$parseBody = SanitizeParams::sanitize('webPage', $parseBody);

		// SALVAR CREATIVE WORK
		$creativeWorkDataCreated = parent::create($parseBody);
		if ($creativeWorkDataCreated['status'] === false) {
			return $creativeWorkDataCreated;
		}
		$valueCreativeWork = $creativeWorkDataCreated['data'][0];
		$parseBody['creativeWork'] = $valueCreativeWork['idcreativeWork'];
		$parseBody['thing'] = $valueCreativeWork['thing'];

		// SALVA WEB PAGE
		$webPageEntity = SupportApplication::getEntity('WebPage');
		$webPageParamsHidrated = $webPageEntity->hydrateParams($parseBody);
		$creativeWorkDataCreated = $this->databaseActions->create('webPage', $webPageParamsHidrated);
		if ($creativeWorkDataCreated['status'] === false) {
			return $creativeWorkDataCreated;
		}

		// SALVA RELACIONAMENTO COM WEB SITE (IsPartOf)
		$relationshipCreated = $this->databaseActions->create('thing_has_thing', ['idHasPart' => $isPartOf, 'typeHasPart' => 'WebSite', 'idIsPartOf' => $parseBody['thing'], 'typeIsPartOf' => 'WebPage']);
		if ($relationshipCreated['status'] === false) {
			return $relationshipCreated;
		}

		//
		$entityHidrated = SupportApplication::hydrateFromDatabase($creativeWorkDataCreated['data']);
		return ['status'=> true, 'message'=> 'WebPage created', 'data'=> SupportApplication::convertFromObjectToSchemaOrgArrayDTO($entityHidrated)];
	}
}
