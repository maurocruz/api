<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\PDO\PDOConnect;
use ReflectionException;

class User extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected string $table = "user";
    /**
     * @var string
     */
    protected string $type = "User";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "name", "status" ];

    /**
     * @param array $params
     * @return string[][]
     */
    public function post(array $params): array
    {
        if (strlen($params['name']) < 5 ) {
            return [ "status" => 'fail', "message" => "The name must be longer than 4 characters" ];
        }

        if (filter_var($params['email'], FILTER_VALIDATE_EMAIL) === false) {
            return [ "status" => 'fail', "message" => "Invalid email" ];
        }

        if(strlen($params['password']) < 8) {
            return [ "status" => 'fail', "message" => "Password must be a minimum of 8 characters" ];
        }

        if(preg_match('@[A-Z]@', $params['password']) === 0) {
            return [ "status" => 'fail', "message" => "Password must contain at least one uppercase character" ];
        }
        if(preg_match('@[a-z]@', $params['password']) === 0) {  
            return [ "status" => 'fail', "message" => "Password must contain at least one lowercase character" ];
        }
        if(preg_match('@[0-9]@', $params['password']) === 0) {  
            return [ "status" => 'fail', "message" => "Password must contain at least 1 number" ];
        }

        $params['password'] = password_hash($params['password'], PASSWORD_DEFAULT);

        $data = parent::created($params);

        if (isset($data['error']) || (isset($data['status']) && $data['status'] == 'error')) {
            return [ 'status' => 'error', 'data' => $data ];
        } else {
            $id = PDOConnect::lastInsertId();
            return ['id'=>$id];
        }
    }

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null) : array
    {
        return parent::createSqlTable('User');
    }
}
