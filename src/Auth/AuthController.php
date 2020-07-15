<?php

namespace Plinct\Api\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Plinct\Api\Type\User;

use Plinct\Api\Auth\Session;

class AuthController extends User
{
    protected $request;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function login($params)
    {
        return parent::login($params);
    }
    
    
    public function logout()
    {
        Session::logout();
        
        return [ "message" => "Session login unseted" ];
    }
}
