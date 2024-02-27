<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;

class WebPage extends Entity
{
	/**
	 * @var string
	 */
	protected string $table = "webPage";
	/**
	 * @var string
	 */
	protected string $type = "WebPage";
	/**
	 * @var array|string[]
	 */
	protected array $properties = [];
	/**
	 * @var array|string[]
	 */
	protected array $hasTypes = [ "hasPart" => "WebPageElement", "identifier" => "PropertyValue", "isPartOf"=>'WebSite'];

	/**
	 * @param array $params
	 * @return array
	 */
	public function post(array $params): array
	{
		return parent::post($this->addBreadcrumb($params));
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::put($this->addBreadcrumb($params));
	}

	/**
	 * @param array|null $params
	 * @return mixed
	 */
	private function addBreadcrumb(array $params = null): array {
		$breadcrumb = new Breadcrumb();
		$bredcrumArray = $breadcrumb->get($params);
		$breadcrumbJson = json_encode($bredcrumArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$params['breadcrumb'] = $breadcrumbJson;
		return $params;
	}
}
