<?php

class DBController {
    public $conn;
    protected $cfg;

    public function __construct(string $db = null)
    {
        $this->cfg = $GLOBALS["cfg"];
        $this->conn = $this->connect([
            "host" => $this->cfg->get("mysql.host"),
            "username" => $this->cfg->get("mysql.username"),
            "password" => $this->cfg->get("mysql.password")
        ], $db);
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
        return $this->conn->errno !== false;
    }

    public function getError(): string {
        return $this->conn->error;
    }
}
