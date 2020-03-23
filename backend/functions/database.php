<?php

$cfg = require dirname(dirname(__FILE__)) . "/config.php";

class DBController {
    public $conn;
    public function __construct(string $db = null)
    {
        global $cfg;
        $this->conn = $this->connect([
            "host" => $cfg->get("mysql.host"),
            "username" => $cfg->get("mysql.username"),
            "password" => $cfg->get("mysql.password")
        ], $db);
        $this->conn->query("SET NAMES utf8");
    }

    public function connect(array $info, string $database = null) {
        return new mysqli($info["host"], $info["username"], $info["password"], $database);
    }

    public function query(string $query) {
        return $this->conn->query($query);
    }

    public function getInstance() {
        return $this->conn;
    }

    public function isOK(): bool {
        return $this->conn->errno !== 0 && empty($this->conn->error);
    }

    public function getError(): string {
        return $this->conn->error;
    }
}
