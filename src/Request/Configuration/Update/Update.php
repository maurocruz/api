<?php
namespace Plinct\Api\Request\Configuration\Update;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Update
{
	public function v2tov3(): array
	{
		$schema_name = PDOConnect::getDbname();

		PDOConnect::run("SET autocommit=0;");

		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/drop_procedures.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/sql_upgrade.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/create_procedures.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/procedure_create_tables.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/procedure_add_foreign_keys.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_imageObject.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_action.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_article.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_book.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_contactPoint.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_event.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_invoice.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_person.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_localBusiness.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_offer.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_order.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_orderItem.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_organization.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_place.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_postalAddress.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_product.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_service.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_taxon.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_videoObject.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_webPage.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_webPageElement.sql'));
		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/upgrade_webSite.sql'));

		$returns = PDOConnect::run("CALL sql_upgrade('$schema_name');");

		PDOConnect::run(file_get_contents(__DIR__ . '/v2tov3/drop_procedures.sql'));

		PDOConnect::run("SET @@autocommit=1;");

		return $returns;
	}
}