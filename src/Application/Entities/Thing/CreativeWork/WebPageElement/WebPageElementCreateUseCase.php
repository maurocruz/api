<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\WebPageElement;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkCreateUseCase;
use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

class WebPageElementCreateUseCase extends CreativeWorkCreateUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function create(array $parseBody) : array
	{
		$parseBody['type'] = $parseBody['type'] ?? 'WebPageElement';
		$isPartOf = $parseBody['isPartOf'] ?? null;
		if (!$isPartOf) {
			return ['status'=> false, 'message'=> 'isPartOf is required'];
		}
		// SANITIZE PARAMS
		$params = SanitizeParams::sanitize('WebPageElement', $parseBody);

		// SALVAR CREATIVE WORK
		$creativeWorkDataCreated = parent::create($params);
		if ($creativeWorkDataCreated['status'] === false) {
			return $creativeWorkDataCreated;
		}
		$valueCreativeWork = $creativeWorkDataCreated['data'][0];
		$params['creativeWork'] = $valueCreativeWork['idcreativeWork'];
		$params['thing'] = $valueCreativeWork['thing'];

		// SALVA WEB PAGE ELEMENT
		$webPageElementEntity = SupportApplication::getEntity('WebPageElement');
		$webPageElementParamsHidrated = $webPageElementEntity->hydrateParams($params);
		$creativeWorkDataCreated = $this->databaseActions->create('WebPageElement', $webPageElementParamsHidrated);
		if ($creativeWorkDataCreated['status'] === false) {
			return $creativeWorkDataCreated;
		}

		// SALVA RELACIONAMENTO COM WEB SITE (IsPartOf)
		$relationshipCreated = $this->databaseActions->create('thing_has_thing', ['idHasPart' => $isPartOf, 'typeHasPart' => 'WebSite', 'idIsPartOf' => $params['thing'], 'typeIsPartOf' => 'WebPageElement']);
		if ($relationshipCreated['status'] === false) {
			return $relationshipCreated;
		}
		//
		$entityHidrated = SupportApplication::hydrateFromDatabase($creativeWorkDataCreated['data']);
		return ['status'=> true, 'message'=> 'WebPageElement created', 'data'=> SupportApplication::convertFromObjectToSchemaOrgArrayDTO($entityHidrated)];
	}
}
