<?php

declare(strict_types=1);

namespace Plinct\Api\Server;

use Plinct\Api\Server\GetData\GetData;
use Plinct\PDO\Crud;
use Plinct\PDO\PDOConnect;

abstract class DatabaseAccessAbstract
{
	/**
	 * @var string
	 */
	protected string $table;
	/**
	 * @var array|null
	 */
	protected ?array $params = null;

	/**
	 * @return array
	 */
	protected function get(): array
	{$returnData = null;

		if($this->params) {
			$getData = new GetData('map_viewport');
			$getData->setParams($this->params);
			$data = $getData->render();
			if (!empty($data)) {
				foreach ($data as $value) {
					$value['viewport'] = json_decode($value['viewport'], true);
					$returnData[] = $value;
				}
				return ['status' => 'success', 'method' => 'GET', 'data' => $returnData];

			} else {
				return ['status' => 'success', 'method' => 'GET','message'=>'Viewport() return empty','data' => $data];
			}
		}
		return ['status'=>'fail','message'=>'Something went wrong!'];
	}
	/**
	 * @return array
	 */
	protected function post(): array
	{
		$crud = new Crud();
		$crud->setTable($this->table);
		$data = $crud->created($this->params);
		if ($data == []) {
			$newId = PDOConnect::lastInsertId();
			$idName = "id$this->table";
			if ($this->table == 'map_viewport') PDOConnect::run("UPDATE `$this->table` SET `position`=`position`+1 WHERE `$idName`!='$newId'");
			return ['status'=>'success','method'=>'POST','data'=>["id$this->table"=>$newId]];
		}
		return ['status'=>'fail','message'=>'Something went wrong!','data'=>$data];
	}

	protected function put(): array
	{
		$idmap_viewport = $this->params['idmap_viewport'];
		unset($this->params['idmap_viewport']);

		$crud = new Crud();
		$crud->setTable($this->table);
		$data = $crud->update($this->params,"`idmap_viewport`='$idmap_viewport'");
		if($data == []) {
			return ['status'=>'success','method'=>'PUT','data'=>$data];
		}
		return ['status'=>'fail','message'=>'Something went wrong!','data'=>$data];
	}

	protected function delete(): array
	{
		$crud = new Crud();
		$crud->setTable($this->table);
		$data = $crud->erase($this->params);

		if (isset($data['message']) && $data['message'] == "Deleted successfully") {
			return ['status'=>'success','method'=>'DELETE','message'=>"Deleted item"];
		}
		return ['status'=>'fail','message'=>'Something went wrong!'];
	}
}