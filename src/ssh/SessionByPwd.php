<?php
namespace ssh;
require_once __DIR__ . "/Credentials/PasswordCredential.php";

use ssh\Credentials\PasswordCredential as PwdCred;

class SessionByPwd
{
    private $source;
    private $conn;
    private $host;
    private $port = "22";
    private $username;
    private $password;

    function __construct($host, $username, $password) {
        $host_arr = explode(":", $host);
        $this->host = $host_arr[0];

        if ($host_arr[1]) {
            $this->port = $host_arr[1];
        }

        $this->username = $username;
        $this->password = $password;
    }

    public function connect() {

        $this->conn = @ssh2_connect($this->host, $this->port);

        if ($this->conn === false) {
            $this->disconnect();
            trigger_error("the remote host " . $this->host . " can't connect", E_USER_WARNING);
            return false;
        }

        $PwdCred = new PwdCred($this->username, $this->password);
        if (!$PwdCred->authenticate($this->conn)) {
            trigger_error("password authority was error!", E_USER_WARNING);
            $this->disconnect();
            return false;
        }

        $this->source = ssh2_shell($this->conn);
        stream_set_blocking ($this->source, true);
    }

    public function send($cmd) {
        if ($this->conn === false) {
            $this->disconnect();
            trigger_error("the remote host is not connected", E_USER_WARNING);
            return false;
        }

        if (!$this->source) {
            trigger_error("the shell source is not exist", E_USER_WARNING);
            return false;
        }

        $cmd = str_replace("\\r", "", trim($cmd));
        $cmd = str_replace("\\n", "", $cmd);
        $cmd = $cmd . "\n";

        fwrite($this->source, $cmd);

    }

    public function expect($str, $timeout = 0, $show = true) {
        if (!$this->source) {
            trigger_error("the shell source is not exist", E_USER_WARNING);
            return false;
        }

        $time = 0;
        $contents = "";
        while (!feof($this->source)) {
            $content = fread($this->source, 8192);
            $contents .= $content;
            if ($show)
                echo $content;
            if (stripos($contents, $str))
                return $contents;
            sleep(1);
            $time++;

            if ($time > $timeout && $timeout > 0)
                break;
        }
    }
    
    public function exec($cmd) {
        if ($this->conn === false) {
            $this->disconnect();
            trigger_error("the remote host is not connected", E_USER_WARNING);
            return false;
        }
       

        $source = ssh2_exec($this->conn, $cmd);
        stream_set_blocking ($source, true);
        $contents = "";
        while ($line = fgets ($source)) {
            $contents .= $line;
        }
        return $contents;
    }

    public function disconnect() {
        $this->conn = FALSE;

        if ($this->source) {
            fclose($this->source);
        }
    }

    public function isConnected() {
        if ($this->conn)
            return TRUE;

        return FALSE;
    }
}
