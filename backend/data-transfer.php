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

    case "get-agreement":
        require_once "vendor/parsedown.php";
        $parser = new Parsedown();
        $agreement = file_get_contents("agreement.md");
        echo $parser->text($agreement);
    break;

    case "get-applications":
        $limit = $data["page"];
        require_once "functions/database.php";
        $conn = new DBController($cfg, "oasis");
        $start = 10 * ($limit - 1);
        $end = 10;
        $result = $conn->query("SELECT id, username, email, lgbt, status FROM applications WHERE removed='no' LIMIT $start, $end");
        if ($conn->isOK()) {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC), JSON_UNESCAPED_UNICODE);
        } else {
            echo $conn->getError();
        }
    break;

    case "remove-application":
        require_once "functions/database.php";
        $conn = new DBController($cfg, "oasis");
        $id = $data["id"];
        if (is_array($id)) {
            foreach ($id as $value) {
                $conn->query("UPDATE applications set removed='yes' WHERE id='$value'");
            }
        } else {
            $conn->query("UPDATE applications SET removed='yes' WHERE id='$id'");
        }
        if ($conn->isOK()) {
            echo 'ok';
        } else {
            echo $conn->getError();
        }
    break;

    case "get-max-page":
        require_once "functions/database.php";
        $conn = new DBController($cfg, "oasis");
        $result = $conn->query("SELECT * FROM applications WHERE removed='no'");
        if ($conn->isOK()) {
            echo ceil($result->num_rows / 10); // min value 0
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

    case "get":
        require "functions/data-getter.php";
        require "functions/database.php";
        $conn = new DBController($cfg, "oasis");

        if (is_array($data)) {
            $Getter = new Getter($conn);
            foreach ($data as $value) {
                $Getter->get($value, true);
            }
            echo json_encode($Getter->getResult());
        } else {
            $Getter->get($value);
            echo $Getter->getResult();
        }
    break;
}
