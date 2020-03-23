<?php

require "functions/database.php";
require "functions/verification.php";

$data = $_POST;

function submit(array $data) {
    $conn = new DBController("oasis");
    $ver = new Verification($data, $conn->getInstance());
    
    if ($ver->isBlacklisted()) {
        return 'blacklisted';
    } else if ($ver->isCommitable()) {
        if ($ver->verify()) {
            extract($data);
            $conn->query("INSERT INTO applications (username, age, gender, email, come_from, lgbt, focusing, introduction, want_to_do, preferred_games, links)
            VALUES ('$username', '$age', '$gender', '$email', '$come_from', '$lgbt', '$focusing', '$introduction', '$want_to_do', '$preferred_games', '$links')");
            if ($conn->isOK()) {
                return 'ok';
            } else {
                return $conn->getError();
            }
        } else {
            return $ver->error;
        }
    } else {
        return $ver->reason;
    }
    
}

echo submit($data);





