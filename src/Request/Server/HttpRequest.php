<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Actions\Permissions;

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

	public function getTable(): string
	{
		return $this->classActions->getTable();
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
			? ApiFactory::user()->privileges()->filterGet($this->classActions->get($params))
			: ApiFactory::response()->message()->fail()->userNotAuthorizedForThisAction(__FILE__.' on line '.__LINE__);
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadedFiles
	 * @return array
	 */
	public function post(array $params = null, array $uploadedFiles = null): array
	{
		if(Permissions::isRequiresSubscription()) {
			$data = $this->classActions->post($params, $uploadedFiles);
			return empty($data) ? ApiFactory::response()->message()->success('Item added') : $data;
		} else {
			return ApiFactory::response()->message()->fail()->userNotAuthorizedForThisAction(__FILE__ . ' on line ' . __LINE__);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		if (Permissions::isRequiresSubscription()) {
			$idname = "id".$this->classActions->getTable();
			$idvalue = $params[$idname] ?? null;
			if ($idvalue) {
				//$filter = ApiFactory::user()->privileges()->filterGet($this->classActions->get([$idname=>$idvalue]),'put');
				//if(isset($filter['status']) && $filter['status'] == 'fail') {
					//return $filter;
				//} else {
					$putdata = $this->classActions->put($params);
					if (empty($putdata)) {
						return ApiFactory::response()->message()->success('Updated data', $putdata);
					} elseif (isset($putdata['status']) && $putdata['status'] == 'success') {
						return  $putdata;
					}
					return ApiFactory::response()->message()->fail()->generic($putdata);
				//}
			}
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(__FILE__.' on line '.__LINE__);
		}
		return ApiFactory::response()->message()->fail()->userNotAuthorizedForThisAction(__FILE__.' on line '.__LINE__);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		if (Permissions::isRequiresSubscription()) {
			$filter = ApiFactory::user()->privileges()->filterGet($this->classActions->get($params),'delete');
			if(isset($filter['status']) && $filter['status'] == 'fail') {
				return $filter;
			} else {
				$returns = $this->classActions->delete($params);
				if ($returns == []) {
					return ApiFactory::response()->message()->success('successfully deleted', $params);
				} else {
					return ApiFactory::response()->message()->fail()->generic($returns);
				}
			}
		}
		return  ApiFactory::response()->message()->fail()->userNotAuthorizedForThisAction(__FILE__.' on line '.__LINE__);
	}
}
