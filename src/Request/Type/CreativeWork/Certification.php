<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\HttpRequestInterface;

class Certification extends Entity implements HttpRequestInterface
{

	public function __construct()
	{
		$this->setTable('certification');
	}

	public function get(array $params = []): array
	{
		$returns = [];
		$properties = $params['properties'] ?? null;
		$dataCert = parent::getData($params);
		if (!empty($dataCert) && $properties) {
			foreach ($dataCert as $certificate) {
				$about = $certificate['about'];
				if (strpos($properties, 'about') !== false) $certificate['about'] = parent::getProperties('thing', ['idthing' => $about, 'properties' => 'image'])[0];
				$returns[] = $certificate;
			}
		} else {
			$returns = $dataCert;
		}
		return parent::sortData($returns);
	}

	public function post(array $params = null): array
	{
		$params['type'][] = "Certification";
		$certificationIdentification = $params['certificationIdentification'] ?? null;
		$issuedBy = $params['issuedBy'] ?? null;
		if ($certificationIdentification && $issuedBy) {
			// SAVE CREATIVEWORK
			return $this->createWithParent('creativeWork', $params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: certificationIdentification and issuedBy']);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::update('creativeWork', $params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('creativeWork', $params);
	}
}
