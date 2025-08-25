<?php

namespace Plinct\Api\Request\Configuration;

class Home
{
	public function ready(): array
	{
		return [
			"@context" => "https://schema.org/",
			"@graph" => [
				[
					"@type" => "SoftwareApplication",
					"name" => "Plinct API",
				]
			]
		];
	}

}
