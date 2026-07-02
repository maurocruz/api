<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\WebSite;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkCreateUseCase;
use ReflectionException;

class WebSiteCreateUseCase extends CreativeWorkCreateUseCase
{

	/**
	 * @throws ReflectionException
	 */
	public function create(array $sanitizedParams): array
	{
		$sanitizedParams['type'] = $sanitizedParams['type'] ?? 'WebSite';

		// SALVAR CREATIVE WORK
		$creativeWorkDataCreated = parent::create($sanitizedParams);
		if ($creativeWorkDataCreated['status'] === false) {
			return $creativeWorkDataCreated;
		}
		$valueCreativeWork = $creativeWorkDataCreated['data'][0];
		$sanitizedParams['creativeWork'] = $valueCreativeWork['idcreativeWork'];
		$sanitizedParams['thing'] = $valueCreativeWork['thing'];

		// SALVAR WEB SITE
		return $this->databaseActions->create('webSite', $sanitizedParams);
	}
}
