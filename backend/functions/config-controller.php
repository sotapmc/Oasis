<?php

class Config
{
    private $config;
    private $path;
    public function __construct(array $conf)
    {
        $this->config = $conf;
    }

    public function get(string $path)
    {
        $this->path = $path;
        $arr = explode(".", $path);
        for ($i = 0; $i < count($arr); $i++) {
            $result = $i === 0 ? $this->config[$arr[$i]] : $result[$arr[$i]];
        }
        return $this->filter($result);
    }

    public function filter(string $value)
    {
        switch ($this->path) {
            case "oasis.max-app-per-page":
                if (!($value >= 5 && $value <= 50)) {
                    return 5;
                }

                // no default case needed
        }
        return $value;
    }
}
