<?php
namespace Plinct\Api\Infrastructure\Persistence\Database\Update\UpdateV2ToV3;

use Exception;
use Plinct\Api\Infrastructure\Persistence\Database\ConnectDB;

class UpdateV2ToV3
{
	private const string SQL_FILE_ERROR_MESSAGE = 'Error in file';

	/**
	 * @throws Exception
	 */
	public function updateV2ToV3(): array
	{
		$schemaName = ConnectDB::getDbname();

		ConnectDB::run('SET autocommit=0;');

		// apagar todos os indices e chaves estrangeiras
		ConnectDB::run(file_get_contents(__DIR__ . '/create_procedures.sql'));
		ConnectDB::run("CALL drop_keys('$schemaName');");


		// atualizar entidades
		$operations = [
			['entity' => 'thing', 'mode' => 'create'],
			['entity' => 'creativeWork', 'mode' => 'create'],
			['entity' => 'mediaObject', 'mode' => 'create'],
			['entity' => 'user', 'mode' => 'upgrade'],
			['entity' => 'action', 'mode' => 'create'],
			['entity' => 'contactPoint', 'mode' => 'upgrade'],
			['entity' => 'postalAddress', 'mode' => 'upgrade'],
			['entity' => 'propertyValue', 'mode' => 'upgrade'],
			['entity' => 'imageObject', 'mode' => 'upgrade'],
			['entity' => 'person', 'mode' => 'upgrade'],
			['entity' => 'article', 'mode' => 'upgrade'],
			['entity' => 'book', 'mode' => 'upgrade'],
			['entity' => 'videoObject', 'mode' => 'upgrade'],
			['entity' => 'webSite', 'mode' => 'upgrade'],
			['entity' => 'webPage', 'mode' => 'upgrade'],
			['entity' => 'webPageElement', 'mode' => 'upgrade'],
			['entity' => 'place', 'mode' => 'upgrade'],
			['entity' => 'event', 'mode' => 'upgrade'],
			['entity' => 'organization', 'mode' => 'upgrade'],
			['entity' => 'product', 'mode' => 'upgrade'],
			['entity' => 'taxon', 'mode' => 'upgrade'],
			['entity' => 'trip', 'mode' => 'upgrade'],
			['entity' => 'localBusiness', 'mode' => 'upgrade'],
			['entity' => 'service', 'mode' => 'upgrade'],
			['entity' => 'order', 'mode' => 'upgrade'],
			['entity' => 'offer', 'mode' => 'upgrade'],
			['entity' => 'orderItem', 'mode' => 'upgrade'],
			['entity' => 'invoice', 'mode' => 'upgrade']
		];

		foreach ($operations as $operation) {
			$result = $this->runSqlOperations($operation['entity'], $operation['mode']);
			if ($result !== null) {
				return $result;
			}
		}

		ConnectDB::run('DROP PROCEDURE IF EXISTS drop_keys;');
		ConnectDB::run('DROP PROCEDURE IF EXISTS set_image_in_thing;');
		ConnectDB::run('DROP PROCEDURE IF EXISTS insert_thing_has_thing;');
		ConnectDB::run('SET autocommit=1;');

		return ['status' => true, 'message' => 'Update v2 to v3 implemented', 'data' => []];
	}


	private function runSqlOperations(string $tableName, string $mode): ?array
	{
		if ($mode === 'upgrade' && !empty(ConnectDB::tableExists($tableName)) === false) {
			return null;
		}

		$file  = $mode === 'create' ? __DIR__ . "/../../Structure/$tableName.sql" : __DIR__ . '/upgrade_' . $tableName . '.sql';
		$result = ConnectDB::run(file_get_contents($file));

		if (!empty($result)) {
			return ['status' => false, 'message' => self::SQL_FILE_ERROR_MESSAGE, 'data' => $result];
		}

		return null;
	}
}
