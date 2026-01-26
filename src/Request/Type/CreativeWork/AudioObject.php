<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\HttpRequestInterface;
use Plinct\Api\Request\Server\Relationship;

class AudioObject extends MediaObject implements HttpRequestInterface
{
	/**
	 *
	 */
	public function __construct(Relationship $relationship = null)
	{
		parent::__construct($relationship);
		$this->setTable('audioObject');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$getData = new GetData('audioObject');
		$getData->setParams($params);
		$getData->setLeftJoin('creativeWork','creativeWork.idcreativeWork=audioObject.creativeWork');
		$getData->setLeftJoin('mediaObject','mediaObject.idmediaObject=audioObject.mediaObject');
		$data = $getData->render();
		return parent::sortData($data);
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadfiles
	 * @return array
	 * @throws Exception
	 */
	public function post(array $params = null, array $uploadfiles = null): array
	{
		$params['type'] = 'AudioObject';
		return parent::post($params);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		$idaudioObject = $params['idaudioObject'] ?? $params['audioObject'] ?? null;
		$idthing = $params['thing'] ?? $params['idthing'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		$idIsPartOf = $params['idIsPartOf'] ?? null;
		$representativeOfPage = $params['representativeOfPage'] ?? null;
		$position = $params['position'] ?? null;
		$caption = $params['caption'] ?? null;
		if ($idHasPart && $idIsPartOf) {
			if ($representativeOfPage !== null) $paramsu['representativeOfPage'] = $representativeOfPage;
			if ($position !== null) $paramsu['position'] = $position;
			if ($caption !== null) $paramsu['caption'] = $caption;
			return parent::updateRelationship($idHasPart, $idIsPartOf, $paramsu ?? []);
		} elseif ($idaudioObject || $idthing) {
			return parent::update('mediaObject', $params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: idaudioObject']);
		}
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('mediaObject', $params);
	}
}
