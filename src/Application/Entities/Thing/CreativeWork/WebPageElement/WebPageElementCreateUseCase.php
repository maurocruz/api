<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\WebPageElement;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkCreateUseCase;
use ReflectionException;

class WebPageElementCreateUseCase extends CreativeWorkCreateUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function create(array $sanitizedParams) : array
	{
		$sanitizedParams['type'] = $sanitizedParams['type'] ?? 'WebPageElement';
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

		// SALVA WEB PAGE ELEMENT
		$creativeWorkDataCreated = $this->databaseActions->create('WebPageElement', $sanitizedParams);
		if ($creativeWorkDataCreated['status'] === false) {
			return $creativeWorkDataCreated;
		}

		// SALVA RELACIONAMENTO COM WEB SITE (IsPartOf)
		return $this->databaseActions->create('thing_has_thing', ['idHasPart' => $isPartOf, 'typeHasPart' => 'WebSite', 'idIsPartOf' => $sanitizedParams['thing'], 'typeIsPartOf' => 'WebPageElement']);
	}
}
