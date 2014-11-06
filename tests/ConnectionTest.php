<?php
require_once __DIR__ . "/../src/ssh/Connection.php";

use ssh\Connection;

class ConnectionText extends PHPUnit_Framework_TestCase
{

    public function testConnection() {
        $conn = new Connection('testhost');
        $conn->authByPassword('rannk', 'password');
        $s1 = $conn->createSession();
        $this->assertTrue($s1->isConnected(), "host is connected");

        return $s1;
    }

    /**
     * 
     * @depends testConnection
     */
    public function testExpect($s1) {
        $message = $s1->expect('$', 0, false);
        $this->assertTrue(stripos($message, '$') > 0);
        return $s1;
    }

    /**
     * 
     * @depends testExpect
     * 
     */
    public function testSendCommand($s1) {
        $s1->send("su -");
        $message = $s1->expect("Password:", 0, false);
        $this->assertTrue(stripos($message, 'word:') > 0);
        $s1->send("password1!");
        $message = $s1->expect("#", 3, false);
        $this->assertTrue(stripos($message, '#') > 0);
        return $s1;
    }
    /**
     * 
     * @depends testSendCommand
     */
    public function testExec($s1) {
        $contents = $s1->exec("whoami");    
        $this->assertTrue($contents == "rannk\n");
    }

}
