<?php
namespace Plinct\Api\Helper;

use Exception;

class Helper
{
	/**
	 * @param string $filename
	 * @param string $type
	 * @return ParserMidia
	 * @throws Exception
	 */
	public function ParserMidia(string $filename, string $type): ParserMidia
	{
		return new ParserMidia($filename, $type);
	}
}
