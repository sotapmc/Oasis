<?php

require_once "main.php";
require_once "functions/database.php";

$conn = new DBController("oasis");
$cfg = $GLOBALS["cfg"];

$post = $_POST;
$data = $post["data"];
$action = $post["action"];
$type = $post["type"];

switch ($type) {
    case "GET":

        switch ($action) {
            case "agreement":
                require_once "vendor/parsedown.php";
                $parser = new Parsedown();
                $agreement = file_get_contents("agreement.md");
                echo $parser->text($agreement);
                break;

            case "applications":
                // 按照 config 中所规定的分页数量获取多个 applications，且仅获取选中的简要信息，用于在表格中查看大概。
                $limit = $data["page"];
                $start = $cfg->get("oasis.max-app-per-page") * ($limit - 1);
                $end = $cfg->get("oasis.max-app-per-page");
                $result = $conn->query("SELECT id, username, email, lgbt, status FROM applications WHERE removed='no' LIMIT $start, $end");
                if ($conn->isOK()) {
                    echo json_encode($result->fetch_all(MYSQLI_ASSOC), JSON_UNESCAPED_UNICODE);
                } else {
                    echo $conn->getError();
                }
                break;

            case "application":
                // 获取单个 application 的完整信息，用于显示整个 application 的内容以供审核。
                $id = $data["id"];
                $result = $conn->query("SELECT * FROM applications WHERE id='$id'");
                if ($conn->isOK()) {
                    echo json_encode($result->fetch_array(MYSQLI_ASSOC), JSON_UNESCAPED_UNICODE);
                } else {
                    echo $conn->getError();
                }
                break;

            case "config":
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

            case "data":
                // data 是指一些可以通过简单计算得出的数据，通常为简单的字符串或数字等。
                require "functions/data-getter.php";
                $Getter = new Getter($conn);

                if (is_array($data["name"])) {
                    foreach ($data["name"] as $value) {
                        $Getter->get($value, true);
                    }
                    echo json_encode($Getter->getResult());
                } else {
                    $Getter->get($data["name"]);
                    echo $Getter->getResult();
                }
                break;

            case "review":
                $id = $data["id"];
                $result = $conn->query("SELECT * FROM review WHERE id='$id'");
                if ($conn->isOK()) {
                    echo json_encode($result->fetch_array(MYSQLI_ASSOC), JSON_UNESCAPED_UNICODE);
                } else {
                    echo $conn->getError();
                }
                break;
        }
        break;

    case "TOGGLE":
        switch ($action) {
            // 切换一个 application 是否为 removed 状态
            case "remove-application":
                $id = $data["id"];
                $toggle_action = $data["toggle_action"];
                if ($toggle_action) {
                    $status = "yes";
                } else {
                    $status = "no";
                }
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

            case "spam-application":
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
                        VALUES ('$id', '', '$overall_rank', '$basic_rank', 'yes')");
                }
                if ($conn->isOK()) {
                    echo "ok";
                } else {
                    echo $result->getError();
                }
                break;
        }

    case "ALTER":
        switch ($action) {
            case "application-status":
                extract($data);
                $conn->query("UPDATE applications SET status='$status' WHERE id='$id'");
                if ($conn->isOK()) {
                    echo "ok";
                } else {
                    echo $conn->getError();
                }
                break;
        }
        break;

    case "SUBMIT":
        switch ($action) {
            case "application":
                require_once "functions/verification.php";
                $ver = new Verification($data, $conn);
                if ($ver->isCommitable()) {
                    if ($ver->verify()) {
                        foreach ($data as $key => $value) {
                            // 进行转义避免 MYSQL 语句执行失败。
                            $data[$key] = $conn->getInstance()->real_escape_string($value);
                        }
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

            case "review":
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
        }
}
