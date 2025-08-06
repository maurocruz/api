<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\HttpRequestInterface;

class AudioObject extends MediaObject implements HttpRequestInterface
{
	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
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
		if ($idaudioObject) {
			$dataAudioObject = self::get(['idaudioObject'=>$idaudioObject]);
			if (isset($dataAudioObject[0])) {
				$idmediaObject = $dataAudioObject[0]['mediaObject'];
				$params['idmediaObject'] = $idmediaObject;
				return parent::put($params);
			} else {
				return ApiFactory::response()->message()->fail()->generic($params,'AudioObject is not found');
			}
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
		$idaudioObject = $params['idaudioObject'] ?? $params['audioObject'] ?? null;
		if ($idaudioObject) {
			$dataAudioObject = self::get(['idaudioObject'=>$idaudioObject]);
			if (isset($dataAudioObject[0])) {
				$idmediaObject = $dataAudioObject[0]['mediaObject'];
				$params['idmediaObject'] = $idmediaObject;
				return parent::delete($params);
			} else {
				return ApiFactory::response()->message()->fail()->generic($params,'AudioObject is not found');
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: idaudioObject']);
		}
	}
}
