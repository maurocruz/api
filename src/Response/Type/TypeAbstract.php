<?php
declare(strict_types=1);
namespace Plinct\Api\Response\Type;

abstract class TypeAbstract
{
	/**
	 * @param array $data
	 * @return array
	 */
	protected function extractThing(array $data): array
	{
		foreach ($data as $key => $value) {
			if ($key === 'thing') {
				$data['thing'] = $value['idthing'];
				$data['name'] = $value['name'];
				$data['alternateName'] = $value['alternateName'];
				$data['additionalType'] = $value['additionalType'];
				$data['description'] = $value['description'];
				$data['disambiguatingDescription'] = $value['disambiguatingDescription'];
				$data['url'] = $value['url'];
			}
		}
		return $data;
	}
}
