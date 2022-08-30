<?php

namespace Godforheart\Canal\Kernel\Traits;

use Godforheart\Canal\Kernel\Config;

trait WithConfig
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param $config array|Config
     */
    public function __construct($config)
    {
        $this->config = is_array($config) ? new Config($config) : $config;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function setConfig($key, $value)
    {
        $this->config->set($key, $value);
        return $this;
    }
}