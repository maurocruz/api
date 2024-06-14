<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class PostalAddress extends Entity
{

	public function __construct()
	{
		$this->setTable('postalAddress');
	}

	public function get(array $params = []): array
	{
		return parent::getData($params);
	}

	public function post(array $params = null): array
	{
		$streetAddress = $params['streetAddress'] ?? null;
		if ($streetAddress) {
			$params['name'] = $streetAddress;
			$params['type'][] = "PostalAddress";
			$dataPostalAddress = parent::createWithParent('thing',$params);
			if (isset($dataPostalAddress[0])) {
				$idpostalAddress = $dataPostalAddress[0]['idpostalAddress'];
				$getPostalAddress = ApiFactory::request()->type('postalAddress')->get(['idpostalAddress'=>$idpostalAddress])->ready();
				if (!empty($getPostalAddress)) {
					return ApiFactory::response()->type('postalAddress')->setData($getPostalAddress)->ready();
				} else {
					return ApiFactory::response()->message()->fail()->generic($getPostalAddress);
				}
			} else {
				return ApiFactory::response()->message()->fail()->generic($dataPostalAddress);
			}

		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: streetAddress']);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::update('thing', $params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('thing', $params);
	}
}
