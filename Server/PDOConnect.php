<?php

namespace Fwc\Api\Server;

class PDOConnect 
{
    private static $PDOConnect;
    
    private static $DRIVER;
    private static $HOST;
    private static $DBNAME;
    
    private static $USERPUBLIC;
    private static $PASSWORDPUBLIC;
    
    private static $USERNAMEADMIN;
    private static $EMAILADMIN;
    private static $PASSWORDADMIN;

    public static function connect($driver, $host, $dbname, $username, $password, $options = []) 
    {
        self::$DRIVER = $driver;
        self::$HOST = $host;
        self::$DBNAME = $dbname;
        
        if(self::$PDOConnect == null) {
            $default_options = [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => true
            ];

            $options = array_replace($default_options, $options);
                   
            $dsn = $driver . ":host=" . $host . ";dbname=" . $dbname;
            
            try {
                $PDOConnect = new \PDO($dsn, $username, $password, $options);
                                
            } catch (\PDOException $e) {
                $PDOConnect = [ "error" => [
                    "message" => $e->getMessage(),
                    "code" => $e->getCode()
                ]];
                
            } finally {
                
                self::$PDOConnect = $PDOConnect;
            }
        }
        
        return self::$PDOConnect;
    }
    
    public static function disconnect()
    {
        self::$PDOConnect = null;
    }

    public static function getPDOConnect()
    {
        return self::$PDOConnect;
    }
    
    public static function getDrive()
    {
        return self::$DRIVER;
    }
    
    public static function getHost()
    {
        return self::$HOST;
    }
    
    public static function getDbname()
    {
        return self::$DBNAME;
    }
    
    public static function getUsernameAdmin()
    {
        return self::$USERNAMEADMIN;
    }
    
    public static function getEmailAdmin()
    {
        return self::$EMAILADMIN;
    }
    
    public static function getPasswordAdmin()
    {
        return self::$PASSWORDADMIN;
    }
    
    public static function setUserPublic($userPublic) 
    {
        self::$USERPUBLIC = $userPublic;
    }
    
    public static function setPasswordPublic($passwordPublic) 
    {
        self::$PASSWORDPUBLIC = $passwordPublic;
    }
    
    public static function setUsernameAdmin($usernameAdmin) 
    {
        self::$USERNAMEADMIN = $usernameAdmin;
    }
    
    public static function setEmailAdmin($emailAdmin) 
    {
        self::$EMAILADMIN = $emailAdmin;
    }
    
    public static function setPasswordAdmin($passwordAdmin) 
    {
        self::$PASSWORDADMIN = $passwordAdmin;
    }
    
    public static function reconnectToAdmin() 
    {
        self::disconnect(); 
        self::connect(self::$DRIVER, self::$HOST, self::$DBNAME, self::$USERNAMEADMIN ?? self::$USERPUBLIC, self::$PASSWORDADMIN ?? self::$PASSWORDPUBLIC);
    }
    
    public static function reconnectToPublic() 
    {
        self::disconnect();
        self::connect(self::$DRIVER, self::$HOST, self::$DBNAME, self::$USERPUBLIC, self::$PASSWORDPUBLIC);
    }


    public static function run($query, $args = NULL) 
    {
         $connect = self::$PDOConnect;
     
        try {
            if ($connect && !array_key_exists('error', $connect)) {
                $q = $connect->prepare($query);
                $q->setFetchMode(\PDO::FETCH_ASSOC);
                
                $q->execute($args);
                $errorInfo = $q->errorInfo();

                if ($errorInfo[0] == "0000") {        
                    return $q->fetchAll();

                } else {
                    throw new \PDOException();
                }   
            } else {
                throw new \PDOException();
            }
            
        } catch (\PDOException $e) {
            
            if(array_key_exists('error', $connect)) {
                return $connect;
                
            } elseif ($errorInfo !== '0000') {                
                return [ "error" => [ 
                    "message" => $errorInfo[2],
                    "code" => $errorInfo[1],
                    "query" => $query
                ] ];
            } else {
                return [ "error" => [ 
                    "message" => $e->getMessage(),
                    "code" => $e->getCode()
                ] ];
            }
        }
    }
}
