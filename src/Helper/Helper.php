<?php
namespace Plinct\Api\Helper;

use Exception;

class Helper
{
	/**
	 * @param string $filename
	 * @param string $type
	 * @param int $size
	 * @return ParserMidia
	 * @throws Exception
	 */
	public function ParserMidia(string $filename, string $type, int $size): ParserMidia
	{
		return new ParserMidia($filename, $type, $size);
	}
}
