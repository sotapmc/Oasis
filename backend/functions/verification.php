<?php
class Verification {
    protected $data;
    protected $conn;
    protected $cfg;
    public $error;
    public $reason;
    public $data_ver;

    public function __construct(array $data, mysqli $connect, Config $config) {
        $this->data = $data;
        $this->error = [];
        $this->conn = $connect;
        $this->cfg = $config;
    }

    public function verify() {
        extract($this->data);
        if ($this->cfg->get("oasis.agreement-enabled") === true && $agreement !== true){
            return 'bad_agreement';
        }
        $age = intval($age);
        $this->data_ver = [
            "username" => preg_match("/^[a-zA-Z0-9_]{3,18}$/", $username),
            "age" => empty($age) ? true : $age >= 13 && $age <= 120,
            "gender" => $gender === "male" || $gender === "female" || $gender === "secret" || $gender === "others",
            "email" => preg_match("/^[A-Za-z0-9]+([_\.][A-Za-z0-9]+)*@([A-Za-z0-9\-]+\.)+[A-Za-z]{2,6}$/", $email) && mb_strlen($email) !== 0,
            "come_from" => in_array($come_from, ["mcbbs", "douban", "nga", "mcmod", "zhihu", "recommended", "other"]),
            "lgbt" => $lgbt === 'yes' || $lgbt === 'no',
            "links" => preg_match("!https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)!", $links),
            "contents" => mb_strlen($focusing) > 2 && $this->required($introduction, $want_to_do, $preferred_games)
        ];
        $a = true;
        foreach ($this->data_ver as $key => $value) {
            if ($value === false) {
                array_push($this->error, $key);
                $a = false;
            }
        }
        return $a;
        //return !in_array(false, $verify);
    }

    public function isCommitable(): bool {
        $username = $this->data["username"];
        // 判断是否为未审核状态，如果是则不允许重复申请
        $r1 = $this->conn->query("SELECT * FROM applications WHERE username='$username' AND status='pending' AND removed='no'");
        if ($r1->num_rows > 0) {
            $this->reason = "pending";
            return false;
        }
        // 判断是否申请超过3次，如果是则不允许申请
        $r2 = $this->conn->query("SELECT * FROM applications WHERE username='$username'");
        if ($r2->num_rows > $this->cfg->get("oasis.max-app-per-player")) {
            $this->reason = "too_much";
            return false;
        }
        // 判断是否为黑名单
        $r3 = $this->conn->query("SELECT * FROM applications WHERE username='$username' AND status='blacklisted'");
        if ($r3->num_rows > 0) {
            $this->reason = "blacklisted";
            return false;
        }
        return true;
    }

    public function required(...$args): bool {
        $a = true;
        foreach ($args as $key => $value) {
            if (mb_strlen($value) < 20) {
                $this->reason = "too_short";
                return false;
            }
        }
        return true;
    }
}