<?php
namespace ssh;
require_once __DIR__ . "/SessionByPwd.php";

use ssh\SessionByPwd;

class Connection {
    
    private $host;
    private $port = "22";
    private $conn = FALSE;
    private $username;
    private $password;
    private $authType = "password";
    
    
    function __construct($host, $port = "22"){
        $this->host = $host;
        $this->port = $port;
    }
    
    public function authByPassword($username, $password) {
        $this->username = $username;
        $this->password = $password;
        $this->authType = "password";
    }
    
    public function setHost($host){
        $this->host = $host;
    }
    
    public function setPort($port){
        $this->port = $port;
    }
    
    public function createSession(){
        if($this->authType == "password") {
            $session = new SessionByPwd($this->host . ":" . $this->port, $this->username, $this->password);
            $session->connect();
            return $session;
        }
        
        trigger_error("Please select an authentication method. suggestion: authByPassword(username, password)", E_USER_WARNING);
        return false;
    }
    
}
