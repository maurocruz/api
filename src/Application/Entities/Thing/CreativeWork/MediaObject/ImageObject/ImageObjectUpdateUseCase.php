<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\ImageObject;

use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;

readonly class ImageObjectUpdateUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	) { }

	/**
	 * @param array $parseBody
	 * @return array
	 */
	public function update(array $parseBody = []): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SanitizeParams::sanitize('ImageObject', $parseBody);

		// SE FOR APENAS RELACIONAMENTO
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		$idIsPartOf = $sanitizedParams['idIsPartOf'] ?? null;
		if ($idHasPart && $idIsPartOf) {
			return $this->updateRelationships($idHasPart, $idIsPartOf, $sanitizedParams);
		}
		return ['status' => false, 'message' => 'Method imcomplete', 'data' => []];
	}

	/**
	 * @param string $idHasPart
	 * @param string $idIsPartOf
	 * @param array $sanitizedParams
	 * @return array
	 */
	private function updateRelationships(string $idHasPart, string $idIsPartOf, array $sanitizedParams = []): array
	{
		$representativeOfPage = $sanitizedParams['representativeOfPage'] ?? null;
		$caption = $sanitizedParams['caption'] ?? null;
		if (is_null($representativeOfPage) && is_null($caption)) {
			return ['status' => false, 'message' => 'At least one of representativeOfPage or caption is required'];
		}
		if ($representativeOfPage !== null) {
			$executableParams['representativeOfPage'] = $representativeOfPage;
			// ZERAR TODOS OS OUTROS
			$this->databaseActions->update('thing_has_thing', ['representativeOfPage' => 0], ['idHasPart' => $idHasPart, 'typeIsPartOf' => 'ImageObject']);
		}
		if ($caption !== null) {
			$executableParams['caption'] = $caption;
		}
		return $this->databaseActions->update('thing_has_thing', $executableParams ?? [], ['idHasPart' => $idHasPart, 'idIsPartOf' => $idIsPartOf]);
	}

}
