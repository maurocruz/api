<?php
namespace Plinct\Api\Request\Configuration\Update;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Update
{

	private array $errors = [];

	/**
	 * @return array
	 */
	public function v2tov3(): array
	{
		$schema_name = PDOConnect::getDbname();
		PDOConnect::run("SET autocommit=0;");
		// apagar todos os indices e chaves estrangeiras
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/create_procedures.sql'));
		PDOConnect::run("CALL drop_keys('$schema_name');");
		PDOConnect::run("DROP TABLE IF EXISTS `thing_has_thing`;");
		PDOConnect::run("DROP TABLE IF EXISTS `mediaObject`;");
		PDOConnect::run("DROP TABLE IF EXISTS `creativeWork`;");
		PDOConnect::run("DROP TABLE IF EXISTS `thing`;");

		fopen(__DIR__ . '/v2tov3/upgradeV2toV3.sql', 'w');
		file_put_contents(__DIR__ . '/v2tov3/upgradeV2toV3.sql', '-- Update em ' . date('Y-m-d H:i:s') . " ---\n");

		$filesArray = [
			__DIR__ . '/../Module/database/thing.sql',
			__DIR__ . '/../Module/database/creativeWork.sql',
			__DIR__ . '/../Module/database/mediaObject.sql',
			__DIR__ . '/../Module/database/action.sql',
			__DIR__ . '/v2tov3/upgrade_user.sql',
			__DIR__ . '/v2tov3/upgrade_contactPoint.sql',
			__DIR__ . '/v2tov3/upgrade_postalAddress.sql',
			__DIR__ . '/v2tov3/upgrade_propertyValue.sql',
			__DIR__ . '/v2tov3/upgrade_imageObject.sql',
			__DIR__ . '/v2tov3/upgrade_person.sql',
			__DIR__ . '/v2tov3/upgrade_article.sql',
			__DIR__ . '/v2tov3/upgrade_book.sql',
			__DIR__ . '/v2tov3/upgrade_videoObject.sql',
			__DIR__ . '/v2tov3/upgrade_webSite.sql',
			__DIR__ . '/v2tov3/upgrade_webPage.sql',
			__DIR__ . '/v2tov3/upgrade_webPageElement.sql',
			__DIR__ . '/v2tov3/upgrade_place.sql',
			__DIR__ . '/v2tov3/upgrade_event.sql',
			__DIR__ . '/v2tov3/upgrade_organization.sql',
			__DIR__ . '/v2tov3/upgrade_product.sql',
			__DIR__ . '/v2tov3/upgrade_taxon.sql',
			__DIR__ . '/v2tov3/upgrade_localBusiness.sql',
			__DIR__ . '/v2tov3/upgrade_service.sql',
			__DIR__ . '/v2tov3/upgrade_order.sql',
			__DIR__ . '/v2tov3/upgrade_orderItem.sql',
			__DIR__ . '/v2tov3/upgrade_offer.sql',
			__DIR__ . '/v2tov3/upgrade_invoice.sql'
		];
		foreach ($filesArray as $file) {
			if (file_put_contents(__DIR__ . '/v2tov3/upgradeV2toV3.sql', file_get_contents($file), FILE_APPEND) === false) {
				$this->errors[] = "Error in file $file";
			}
		}

		if (!empty($this->errors)) {
			return $this->errors;
		} else {
			$runSql = PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgradeV2toV3.sql'));
			PDOConnect::run("ALTER TABLE `thing` CHANGE COLUMN `lastModified` `lastModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP();");
			PDOConnect::run("DROP PROCEDURE IF EXISTS drop_keys;");
			PDOConnect::run("DROP PROCEDURE IF EXISTS set_image_in_thing;");
			PDOConnect::run("DROP PROCEDURE IF EXISTS insert_thing_has_thing;");
			PDOConnect::run("SET @@autocommit=1;");
			return $runSql;
		}
	}
}
