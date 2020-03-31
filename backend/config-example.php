<?php

/**
 * config-example.php
 * 这是一个配置文件，用来控制 Oasis 在各方面的表现，
 * 你可以根据注释和自己的理解修改这些内容，使其适合您的使用。
 * 
 * 重要：在使用时请将文件名更改为 config.php 即去掉 -example
 */

require_once "functions/config-controller.php";

return new Config([
    // 数据库相关设置
    "mysql" => [
        "host" => "localhost", // 主机名
        "username" => "root", // 用户名
        "password" => "" // 密码
    ],
    // oasis本体设置
    "oasis" => [
        "max-app-per-player" => 3, // 允许每个玩家名最多申请次数（正整数）
        "max-app-per-page" => 10, // 后台管理请求界面每页显示的请求数量（>= 5 & <= 50）
        "agreement-enabled" => false, // 是否启用协议，即必须同意以后才可申请
        "agreement-name" => "", // 协议的名称，例如「《SoTap 居民申请须知》」
        "color-theme" => "blue",
    ]
]);