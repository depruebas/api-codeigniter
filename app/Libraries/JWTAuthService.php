<?php

namespace App\Libraries;

class JWTAuthService
{
    private static $instance = null;
    private $userData = null;
    private $token = null;
    
    private function __construct() {}
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function setData($userData, $token)
    {
        $this->userData = $userData;
        $this->token = $token;
        return $this;
    }
    
    public function getUserId()
    {
        return isset($this->userData->uid) ? $this->userData->uid : null;
    }
    
    public function getToken()
    {
        return $this->token;
    }
    
    public function getUserData()
    {
        return $this->userData;
    }
    
    public function clear()
    {
        $this->userData = null;
        $this->token = null;
        return $this;
    }
}