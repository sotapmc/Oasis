<?php

$cfg = require_once "config.php";

$post = $_POST;

$action = $post["action"];
$data = $post["data"];

switch ($action) {
    case "submit":
        require_once "functions/database.php";
        require_once "functions/verification.php";
        $conn = new DBController($cfg, "oasis");
        $ver = new Verification($data, $conn->getInstance(), $cfg);
        if ($ver->isCommitable()) {
            if ($ver->verify()) {
                extract($data);
                $conn->query("INSERT INTO applications (username, age, gender, email, come_from, lgbt, focusing, introduction, want_to_do, preferred_games, links)
                VALUES ('$username', '$age', '$gender', '$email', '$come_from', '$lgbt', '$focusing', '$introduction', '$want_to_do', '$preferred_games', '$links')");
                if ($conn->isOK()) {
                    echo 'ok';
                } else {
                    echo $conn->getError();
                }
            } else {
                echo json_encode([
                    "error" => $ver->error,
                    "reason" => $ver->reason
                ], JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo $ver->reason;
        }
    break;

    case "get-config":
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                array_push($result, $cfg->get($value));
            }
        } else {
            $result = $cfg->get($data);
        }
        echo is_array($result) ? json_encode($result, JSON_UNESCAPED_UNICODE) : $result;
    break;
}
