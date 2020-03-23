<?php

require_once "functions/database.php";

$conn = new DBController();

$conn->query("CREATE DATABASE IF NOT EXISTS oasis");

$conn->query("USE oasis");

$conn->query("CREATE TABLE applications (
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(20) NOT NULL,
    age VARCHAR(3),
    gender VARCHAR(10) NOT NULL,
    email VARCHAR(50) NOT NULL,
    come_from VARCHAR(1000) NOT NULL,
    lgbt VARCHAR(5) NOT NULL,
    focusing VARCHAR(1000) NOT NULL,
    introduction VARCHAR(1000) NOT NULL,
    want_to_do VARCHAR(1000) NOT NULL,
    preferred_games VARCHAR(1000) NOT NULL,
    links VARCHAR(1000) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    removed VARCHAR(5) DEFAULT 'no'
) AUTO_INCREMENT=1");

if ($conn->isOK()){
    echo 'Done';
} else {
    var_dump($conn->getInstance());
}