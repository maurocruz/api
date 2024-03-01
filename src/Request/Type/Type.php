<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\HttpRequest;

class Type
{
	private string $type;
	/**
	 * @var object|mixed|null
	 */
	private ?object $classActions = null;
	/**
	 * @var array|null
	 */
	private ?array $params = [];
	/**
	 * @var array|null
	 */
	private ?array $uploadedFiles;
	/**
	 * @var string
	 */
	private string $method;
	/**
	 * @var string
	 */
	private string $namespace;

	/**
	 * @param string $type
	 */
	public function __construct(string $type)
	{
		$this->type = $type;
		$this->namespace = $type;
		$classname = __NAMESPACE__.'\\'.ucfirst($type);
		if (class_exists($classname)) {
			$this->classActions = new $classname();
		}
		$classname = __NAMESPACE__.'\\'.ucfirst($type).'\\'.ucfirst($type);
		if (class_exists($classname)) {
			$this->classActions = new $classname();
		}
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadedFiles
	 * @return $this
	 */
	public function post(array $params = null, array $uploadedFiles = null): Type
	{
		$this->method = 'post';
		$this->params = $params;
		$this->namespace = isset($params['tableHasPart']) ? $this->type.','.$params['tableHasPart'] : $this->type;
		$this->uploadedFiles = $uploadedFiles;
		return $this;
	}
	/**
	 * @param array $params
	 * @return $this
	 */
	public function get(array $params): Type
	{
		$this->method = 'get';
		$this->params = $params;
		return $this;
	}

	/**
	 * @param array $params
	 * @return $this
	 */
	public function put(array $params): Type
	{
		$this->method = 'put';
		$this->params = $params;
		return $this;
	}

	/**
	 * @param array|null $params
	 * @return $this
	 */
	public function delete(?array $params): Type
	{
		$this->method = 'delete';
		$this->params = $params;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function exists(): bool
	{
		return !!$this->classActions;
	}

	/**
	 * @return HttpRequest
	 */
	public function httpRequest(): HttpRequest
	{
		return new HttpRequest($this->classActions);
	}

	/**
	 * @return array
	 */
	public function ready(): array
	{
		if (!!$this->classActions) {
			$httpRequest = new HttpRequest($this->classActions);
			switch ($this->method) {
				case 'post':
					return $httpRequest->withPrivileges('c',$this->namespace,2)->post($this->params, $this->uploadedFiles);
				case 'put':
					return $httpRequest->withPrivileges('u', $this->namespace, 2)->put($this->params);
				case 'get':
					return $httpRequest->setPermission()->get($this->params);
				case 'delete':
					return $httpRequest->withPrivileges('d',$this->namespace,2)->delete($this->params);
				default:
					return ApiFactory::response()->message()->fail()->generic();
			}
		} else {
			// check if table exists
			$connectTable = ApiFactory::request()->server()->connectBd($this->type);
			$checkTable = $connectTable->showTableStatus();
			if (isset($checkTable['status']) && $checkTable['status'] === 'success') {
				return $connectTable->read($this->params);
			}
			return ApiFactory::response()->message()->fail()->thisTypeNotExists();
		}
	}
}
