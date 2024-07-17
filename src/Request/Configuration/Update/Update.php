<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Configuration\Update;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Update
{
	public function v2tov3(): array
	{
		// criar as tabelas thing, thing_has_imageObject, thing_has_thing
		PDOConnect::run(file_get_contents(__DIR__.'/../Module/database/sql/thing.sql'));
		// creative work
		PDOConnect::run(file_get_contents(__DIR__.'/../Module/database/sql/creativeWork.sql'));
		// media object
		PDOConnect::run(file_get_contents(__DIR__.'/../Module/database/sql/mediaObject.sql'));
		// drop indexes and foreign keys
		PDOConnect::run(file_get_contents((__DIR__.'/../Update/v2tov3/drop_indexes.sql')));
		// imageObject
		PDOConnect::run("ALTER TABLE `imageObject` CHANGE COLUMN `idimageObject` `idimageObject` INT UNSIGNED NOT NULL AUTO_INCREMENT;");
		PDOConnect::run(file_get_contents(__DIR__.'/../Module/database/sql/imageObject.sql'));
		// transaction imageObject
		PDOConnect::run(file_get_contents(__DIR__.'/../Update/v2tov3/transaction_imageObject.sql'));
		// transaction article
		PDOConnect::run(file_get_contents(__DIR__ . '/../Update/v2tov3/transaction_article.sql'));
		// transaction book
		PDOConnect::run(file_get_contents(__DIR__.'/../Update/v2tov3/transaction_book.sql'));
		// transaction contactPoint


		die();

		return ApiFactory::response()->message()->success("Nothing to update!");
	}
}