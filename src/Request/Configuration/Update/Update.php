<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Configuration\Update;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Update
{
	public function v2tov3(): array
	{
		$schema_name = PDOConnect::getDbname();


		PDOConnect::run("ALTER TABLE `catalog` DROP INDEX `idx_2`;");
		PDOConnect::run("ALTER TABLE `galleries` DROP INDEX `idx_1`;");
		PDOConnect::run("ALTER TABLE `galleries` DROP INDEX `idx_2`;");
		PDOConnect::run("ALTER TABLE `invoice` DROP INDEX `idx_3`;");
		PDOConnect::run("ALTER TABLE `localBusiness` DROP INDEX `idx_1`;");
		PDOConnect::run("ALTER TABLE `localBusiness_has_imageObject` DROP INDEX `idx_1`;");
		PDOConnect::run("ALTER TABLE `product` DROP INDEX `idx_1`;");


		PDOConnect::run("SET autocommit=0;");


		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/procedure_drop_procedures.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/sql_upgrade.sql'));
		PDOConnect::run(file_get_contents(__DIR__.'/v2tov3/procedure_create_tables.sql'));
		PDOConnect::run(file_get_contents(__DIR__.'/v2tov3/procedure_drop_keys.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_imageObject.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_article.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_book.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_contactPoint.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_event.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_invoice.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_person.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_localBusiness.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_organization.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_place.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_product.sql'));


		PDOConnect::run("CALL drop_procedures();");
		$returns = PDOConnect::run("CALL sql_upgrade('$schema_name');");
		PDOConnect::run("CALL drop_procedures();");


		PDOConnect::run("SET @@autocommit=1;");

		return $returns;
	}
}