<?php

declare(strict_types=1);

namespace Plinct\Api\Request;

use Plinct\Api\Interfaces\HttpRequestInterface;
use Plinct\Api\Request\Actions\Permissions;
use Plinct\Api\User\Privileges\Privileges;
use Plinct\Api\Response\ResponseApi;

class HttpRequest implements HttpRequestInterface
{
	/**
	 * @var object|HttpRequestInterface
	 */
	private object $classActions;

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
		Privileges::withPrivileges($actions, $namespace, $function);
		return $this;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array {
		return Permissions::isRequiresSubscription()
			? Privileges::filter($this->classActions->get($params))
			: ResponseApi::message()->fail()->userNotAuthorizedForThisAction(__FILE__.' on line '.__LINE__);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function post(array $params): array {
		return Permissions::isRequiresSubscription()
			? $this->classActions->post($params)
			: ResponseApi::message()->fail()->userNotAuthorizedForThisAction(__FILE__.' on line '.__LINE__);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array {
		return Permissions::isRequiresSubscription()
			? Privileges::filter($this->classActions->put($params), 'put')
			: ResponseApi::message()->fail()->userNotAuthorizedForThisAction(__FILE__.' on line '.__LINE__);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		if (Permissions::isRequiresSubscription()) {
			$filter = Privileges::filter($this->classActions->get($params),'delete');

			if(isset($filter['status']) && $filter['status'] == 'fail') {
				return $filter;
			} else {
				return $this->classActions->delete($params);
			}
		}

		return  ResponseApi::message()->fail()->userNotAuthorizedForThisAction(__FILE__.' on line '.__LINE__);
	}
}
