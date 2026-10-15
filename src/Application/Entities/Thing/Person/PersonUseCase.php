<?php
namespace Plinct\Api\Application\Entities\Thing\Person;

use Plinct\Api\Domain\Entities\Thing\Person\Person;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class PersonUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams = []): array
	{
		$properties = $sanitizedParams['properties'] ?? [];
		$getData = $this->databaseActions->read('person', $sanitizedParams);
		// ADDRESS
		if (in_array('address', $properties)) {
			$getData->setJoin('postalAddress', "postalAddress.idpostalAddress=person.address");
		}
		$data = $getData->render();

		foreach ($data['data'] as $key => $value) {
			$idthing = $value['idthing'];
			$idperson = $value['idperson'];
			$data['data'][$key]['birthDate'] = $value['birthDate'] === '0000-00-00' ? null : $value['birthDate'];
			$data['data'][$key]['deathDate'] = $value['deathDate'] === '0000-00-00' ? null : $value['deathDate'];
			// CONTACT POINT
			if (in_array('contactPoint', $properties)) {
				$getDataContactPoint = $this->databaseActions->read('thing_has_thing', ['typeHasPart' => 'Person', 'typeIsPartOf' => 'ContactPoint', 'idHasPart'=>$idthing]);
				$getDataContactPoint->setJoin('thing', "thing.idthing=idIsPartOf");
				$getDataContactPoint->setJoin('contactPoint',"contactPoint.thing=idIsPartOf");
				$contactPointData = $getDataContactPoint->render();
				if ($contactPointData['status'] === false) {
					return $contactPointData;
				}
				$data['data'][$key]['contactPoint'] = $contactPointData['data'];
			}
			// MEMBER OF
			if (in_array('memberOf', $properties)) {
				$roleData = $this->databaseActions->read('role', ['person' => $idperson])->render();
				if ($roleData['status'] === false) {
					return $roleData;
				}
				$data['data'][$key]['memberOf'] = $roleData['data'];
			}
		}

		$data['data'] = array_map(fn($person) => (new Person($person))->jsonSerialize(), $data['data']);
		return $data;
	}
}
