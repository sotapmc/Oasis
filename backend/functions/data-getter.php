<?php

class Getter
{
    protected $result;
    protected $conn;
    public function __construct(DBController $conn)
    {
        $this->result = [];
        $this->conn = $conn;
    }

    public function get(string $name, bool $multiple = false): bool
    {
        $conn = $this->conn;
        switch ($name) {
            case "total-requests":
                $r = $conn->query("SELECT * FROM applications WHERE removed='no'");
                $result = $r->num_rows;
            break;

            case "passed-requests":
                $r = $conn->query("SELECT * FROM applications WHERE status='passed' AND removed='no'");
                $result = $r->num_rows;
            break;

            case "passed-percentage":
                $r1 = $conn->query("SELECT * FROM applications WHERE removed='no'");
                $all = $r1->num_rows;
                $r2 = $conn->query("SELECT * FROM applications WHERE status='passed' AND removed='no'");
                $passed = $r2->num_rows;
                if ($all !== 0) {
                    $result = floor($passed / $all);
                } else {
                    $result = 0;
                }
            break;

            default:
            return false;
        }
        if ($multiple) {
            array_push($this->result, $result);
        } else {
            $this->result = $result;
        }
        return true;
    }

    public function getResult() {
        return $this->result;
    }
}
