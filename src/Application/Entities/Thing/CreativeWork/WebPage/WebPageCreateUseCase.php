<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\WebPage;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkCreateUseCase;
use Plinct\Api\Domain\Entities\Entities;
use ReflectionException;

class WebPageCreateUseCase extends CreativeWorkCreateUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function create(array $sanitizedParams) : array
	{
		$sanitizedParams['type'] = $sanitizedParams['type'] ?? 'WebPage';
		$isPartOf = $sanitizedParams['isPartOf'] ?? null;
		if (!$isPartOf) {
			return ['status'=> false, 'message'=> 'isPartOf is required'];
		}

		// SALVAR CREATIVE WORK
		$creativeWorkDataCreated = parent::create($sanitizedParams);
		if ($creativeWorkDataCreated['status'] === false) {
			return $creativeWorkDataCreated;
		}
		$valueCreativeWork = $creativeWorkDataCreated['data'][0];
		$sanitizedParams['creativeWork'] = $valueCreativeWork['idcreativeWork'];
		$sanitizedParams['thing'] = $valueCreativeWork['thing'];

		// SALVA WEB PAGE
		$webPageEntity = Entities::getEntity('WebPage');
		$webPageParamsHidrated = $webPageEntity->hydrateParams($sanitizedParams);
		$creativeWorkDataCreated = $this->databaseActions->create('webPage', $webPageParamsHidrated);
		if ($creativeWorkDataCreated['status'] === false) {
			return $creativeWorkDataCreated;
		}

		// SALVA RELACIONAMENTO COM WEB SITE (IsPartOf)
		return $this->databaseActions->create('thing_has_thing', ['idHasPart' => $isPartOf, 'typeHasPart' => 'WebSite', 'idIsPartOf' => $sanitizedParams['thing'], 'typeIsPartOf' => 'WebPage']);
	}
}
