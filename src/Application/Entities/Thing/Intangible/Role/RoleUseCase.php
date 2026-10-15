<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\Role;

use Plinct\Api\Domain\Entities\Thing\Intangible\Role\Role;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class RoleUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	)	{	}

	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams = []): array
	{
		$properties = $sanitizedParams['properties'] ?? [];
		$getData = $this->databaseActions->read('role', $sanitizedParams);
		$data = $getData->render();
		if ($data['status'] === false) {
			return $data;
		}

		foreach ($data['data'] as $key => $value) {
			// MEMBER
			if (in_array('member', $properties)) {
				$dataMember = $this->databaseActions->read('person', ['idperson' => $value['person']])->render();
				if ($dataMember['status'] === false) {
					return $dataMember;
				}
				$data['data'][$key]['member'] = $dataMember['data'][0];
			}
		}
		$data['data'] = array_map(fn($item) => (new Role($item))->jsonSerialize(), $data['data']);
		return $data;
	}
}
