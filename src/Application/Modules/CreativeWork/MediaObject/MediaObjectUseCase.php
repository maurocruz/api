<?php
namespace Plinct\Api\Application\Modules\CreativeWork\MediaObject;

use Plinct\Api\Infrastructure\Persistence\Database\GetData\GetDataInfrastructure;
use Plinct\Api\Infrastructure\Persistence\Database\RelationshipInfrastructure;
use Plinct\Api\Support\ParseToSchema;
use Plinct\Api\Support\SupportUserCase;

readonly class MediaObjectUseCase
{
	public function __construct(private RelationshipInfrastructure $relationship, private ParseToSchema $parseToSchema, private GetDataInfrastructure $getDataInfrastructure)
	{
	}

	public function read(array $params =[]): array
	{
		$properties = SupportUserCase::propertiesToArray($params['properties'] ?? null);
		$idHasPart = $params['idHasPart'] ?? $params['isPartOf'] ?? null;

		if ($idHasPart) {
			$data = $this->relationship->getAboutData(['MediaObject','ImageObject','VideoObject','AudioObject'], $idHasPart, $params);
		} else {
			$this->getDataInfrastructure->setTable('mediaObject');
			$this->getDataInfrastructure->setParams($params);
			$this->getDataInfrastructure->setLeftJoin('creativeWork', 'creativeWork.idcreativeWork=mediaObject.creativeWork');
			$data = $this->getDataInfrastructure->render();

			if (in_array('subjectOf', $properties)) {
				// armazena idthing em array
				$thingIds = [];
				foreach ($data as $value) {
					$thingIds[] = $value['thing'] ?? $value['idthing'] ?? null;
				}
				// SUBJECT
				$subjectData = $this->relationship->getSubjectOf($thingIds);
			}
		}
		// PROPERTIES
		if (isset($data[0]['idmediaObject'])) {
			foreach ($data as $key => $value) {
				$idthing = $value['thing'] ?? $value['idthing'] ?? null;
				// SUBJECT OF
				if ($properties && in_array('subjectOf', $properties) && !empty($subjectData)) {
					$data[$key]['subjectOf'] = $this->parseToSchema->setType('CreativeWork')->setData($subjectData)->ready();
				}
				// HAS PART
				if (in_array('hasPart', $properties)) {
					$dataHasPart = $this->relationship->getHasPart($idthing,'MediaObject', null, $params);
					if (isset($dataHasPart[0])) {
						$data[$key]['hasPart'] = $dataHasPart;
					}
				}
				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$dataIsPartOf = $this->relationship->getIsPartOf($idthing);
					if (isset($dataIsPartOf[0])) {
						$data[$key]['isPartOf'] = $dataIsPartOf;
					}
				}
			}
		}
		if (isset($data[0])) {
			return ['status'=> true, 'data'=> $this->parseToSchema->setType('MediaObject')->setData(SupportUserCase::sortData($data))->ready()];
		} else {
			return ['status'=> false, 'data'=> $data];
		}
	}

}
