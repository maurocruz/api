<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\WebSite;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkCreateUseCase;
use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

class WebSiteCreateUseCase extends CreativeWorkCreateUseCase
{

	/**
	 * @throws ReflectionException
	 */
	public function create(array $parseBody): array
	{
		$parseBody['type'] = $parseBody['type'] ?? 'WebSite';

		// SANITIZE PARAMS
		$parseBody = SanitizeParams::sanitize('webSite', $parseBody);

		// SALVAR CREATIVE WORK
		$creativeWorkDataCreated = parent::create($parseBody);
		if ($creativeWorkDataCreated['status'] === false) {
			return $creativeWorkDataCreated;
		}
		$valueCreativeWork = $creativeWorkDataCreated['data'][0];
		$parseBody['creativeWork'] = $valueCreativeWork['idcreativeWork'];
		$parseBody['thing'] = $valueCreativeWork['thing'];

		// SALVAR WEB SITE
		$webSiteEntity = SupportApplication::getEntity('WebSite');
		$webSiteParamsHidrated = $webSiteEntity->hydrateParams($parseBody);
		$creativeWorkDataCreated = $this->databaseActions->create('webSite', $webSiteParamsHidrated);
		if ($creativeWorkDataCreated['status'] === false) {
			return $creativeWorkDataCreated;
		}
		$entityHidrated = SupportApplication::hydrateFromDatabase($creativeWorkDataCreated['data']);
		//
		return ['status'=> true, 'message'=> 'WebSite created', 'data'=> SupportApplication::convertFromObjectToSchemaOrgArrayDTO($entityHidrated)];
	}
}
