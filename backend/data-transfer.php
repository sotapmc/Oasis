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
                    "reason" => $ver->reason,
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
        $start = $cfg->get("oasis.max-app-per-page") * ($limit - 1);
        $end = $cfg->get("oasis.max-app-per-page");
        $result = $conn->query("SELECT id, username, email, lgbt, status FROM applications WHERE removed='no' LIMIT $start, $end");
        if ($conn->isOK()) {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC), JSON_UNESCAPED_UNICODE);
        } else {
            echo $conn->getError();
        }
        break;

    case "get-application":
        require_once "functions/database.php";
        $conn = new DBController($cfg, "oasis");
        $id = $data["id"];
        $result = $conn->query("SELECT * FROM applications WHERE id='$id'");
        if ($conn->isOK()) {
            echo json_encode($result->fetch_array(MYSQLI_ASSOC), JSON_UNESCAPED_UNICODE);
        } else {
            echo $conn->getError();
        }
        break;

    case "toggle-application":
        require_once "functions/database.php";
        $conn = new DBController($cfg, "oasis");
        $id = $data["id"];
        $action = $data["action"];
        $status = $action === "remove" ? "yes" : "no";
        if (is_array($id)) {
            foreach ($id as $value) {
                $conn->query("UPDATE applications SET removed='$status' WHERE id='$value'");
            }
        } else {
            $conn->query("UPDATE applications SET removed='$status' WHERE id='$id'");
        }
        if ($conn->isOK()) {
            echo 'ok';
        } else {
            echo $conn->getError();
        }
        break;

    case "update-application-status":
        require_once "functions/database.php";
        $conn = new DBController($cfg, "oasis");
        extract($data);
        $conn->query("UPDATE applications SET status='$status' WHERE id='$id'");
        if ($conn->isOK()) {
            echo "ok";
        } else {
            echo $conn->getError();
        }
        break;

    case "get-max-page":
        require_once "functions/database.php";
        $conn = new DBController($cfg, "oasis");
        $result = $conn->query("SELECT * FROM applications WHERE removed='no'");
        if ($conn->isOK()) {
            echo ceil($result->num_rows / $cfg->get("oasis.max-app-per-page")); // min value 0
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

    case "submit-review":
        require "functions/database.php";
        $conn = new DBController($cfg, "oasis");
        $id = $data["id"];
        $r = $conn->query("SELECT * FROM review WHERE id='$id'");
        if ($r->num_rows > 0) {
            foreach ($data as $key => $value) {
                if ($key === "id") {
                    continue;
                }
                $conn->query("UPDATE review SET $key='$value' WHERE id='$id'");
            }
        } else {
            extract($data);
            $conn->query("INSERT INTO review (id, username, comment, overall_rank, basic_rank)
            VALUES ('$id', '$username', '$comment', '$overall_rank', '$basic_rank')");
        }
        if ($data["overall_rank"] + $data["basic_rank"] >= 12) {
            $conn->query("UPDATE applications SET status='passed' WHERE id='$id' AND removed='no'");
        } else {
            $conn->query("UPDATE applications SET status='invalid' WHERE id='$id' AND removed='no'");
        }
        if ($conn->isOK()) {
            echo "ok";
        } else {
            echo $result->getError();
        }
        break;

    case "get-review":
        require "functions/database.php";
        $conn = new DBController($cfg, "oasis");
        $id = $data["id"];
        $result = $conn->query("SELECT * FROM review WHERE id='$id'");
        if ($conn->isOK()) {
            echo json_encode($result->fetch_array(MYSQLI_ASSOC), JSON_UNESCAPED_UNICODE);
        } else {
            echo $conn->getError();
        }
        break;

    case "toggle-spam":
        require "functions/database.php";
        $conn = new DBController($cfg, "oasis");
        $id = $data["id"];
        $r = $conn->query("SELECT spam FROM review WHERE id='$id'");
        if ($r->num_rows > 0) {
            if ($r->fetch_assoc()["spam"] === "yes") {
                $conn->query("UPDATE review SET spam='no' WHERE id='$id'");
            } else {
                $conn->query("UPDATE review SET spam='yes' WHERE id='$id'");
            }
        } else {
            extract($data);
            $conn->query("INSERT INTO review (id, comment, overall_rank, basic_rank, spam)
            VALUES ('$id', '', '0', '0', 'yes')");
        }
        if ($conn->isOK()) {
            echo "ok";
        } else {
            echo $result->getError();
        }
        break;
}
