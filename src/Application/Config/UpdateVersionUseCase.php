<?php
namespace Plinct\Api\Application\Config;

use Exception;
use Plinct\Api\Infrastructure\Persistence\Database\Update\UpdateV2ToV3\UpdateV2ToV3;

readonly class UpdateVersionUseCase
{
	public function __construct(
		private UpdateV2ToV3 $updateV2ToV3
	)	{	}

	/**
	 * @throws Exception
	 */
	public function v2topv3(): array
	{
		return $this->updateV2ToV3->updateV2ToV3();
	}
}
