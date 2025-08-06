<?php
namespace Plinct\Api\Request\Server;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Actions\Permissions;

class HttpRequest implements HttpRequestInterface
{
	/**
	 * @var HttpRequestInterface
	 */
	private HttpRequestInterface $classActions;

	/**
	 * @param HttpRequestInterface $classActions
	 */
	public function __construct(HttpRequestInterface $classActions) {
		$this->classActions = $classActions;
	}

	/**
	 * @param bool $isPermitted
	 * @return $this
	 */
	public function setPermission(bool $isPermitted = true): HttpRequest
	{
		Permissions::setRequiresSubscription($isPermitted);
		return $this;
	}

	/**
	 * @param string $actions
	 * @param string $namespace
	 * @param int|null $function
	 * @return $this
	 */
	public function withPrivileges(string $actions = 'r', string $namespace = '', int $function = null): HttpRequest
	{
		ApiFactory::user()->privileges()->withPrivileges($actions, $namespace, $function);
		return $this;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array {
		return Permissions::isRequiresSubscription()
			? $this->classActions->get($params)
			: ApiFactory::response()->message()->fail()->userNotAuthorizedForThisAction(__FILE__.' on line '.__LINE__);
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadfiles
	 * @return array
	 */
	public function post(array $params = null, array $uploadfiles = null): array
	{
		return Permissions::isRequiresSubscription()
			?  $this->classActions->post($params, $uploadfiles)
			: ApiFactory::response()->message()->fail()->userNotAuthorizedForThisAction(__FILE__ . ' on line ' . __LINE__);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return Permissions::isRequiresSubscription()
			? $this->classActions->put($params)
			: ApiFactory::response()->message()->fail()->userNotAuthorizedForThisAction(__FILE__ . ' on line ' . __LINE__);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return Permissions::isRequiresSubscription()
			? $this->classActions->delete($params)
			: ApiFactory::response()->message()->fail()->userNotAuthorizedForThisAction(__FILE__.' on line '.__LINE__);
	}
}
