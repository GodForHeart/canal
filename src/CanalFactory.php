<?php

namespace Godforheart\Canal;

class CanalFactory
{
    public static function getClient(array $config)
    {
        return new Client\Application(new Client\Config($config));
    }

    public static function getRabbitmq(array $config)
    {
        return new Rabbitmq\Application(new Rabbitmq\Config($config));
    }
}