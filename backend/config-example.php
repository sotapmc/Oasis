<?php

/**
 * config-example.php
 * 这是一个配置文件，用来控制 Oasis 在各方面的表现，
 * 你可以根据注释和自己的理解修改这些内容，使其适合您的使用。
 * 
 * 重要：在使用时请将文件名更改为 config.php 即去掉 -example
 */

require_once "functions/config.php";

return new Config([
    // 数据库相关设置
    "mysql" => [
        "host" => "localhost", // 主机名
        "username" => "root", // 用户名
        "password" => "" // 密码
    ],
]);