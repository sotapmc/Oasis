<?php

class Config {
    private $config;
    public function __construct(array $conf) {
        $this->config = $conf;
    }

    public function get(string $path) {
        $arr = explode(".", $path);
        for ($i = 0; $i < count($arr); $i++) {
            $result = $i === 0 ? $this->config[$arr[$i]] : $result[$arr[$i]];
        }
        return $result;
    }
}
