<?php

namespace Godforheart\Canal\Rabbitmq;

class Config extends \Godforheart\Canal\Kernel\Config
{
    protected $requiredKeys = [
        'flatMessage',
        'host',
        'port',
        'user',
        'password',
        'exchange',
        'type',
        'queue',
    ];

    public function isFlatMessage(): bool
    {
        return (bool)$this->get('flatMessage');
    }

    public function getHost()
    {
        return $this->get('host');
    }

    public function getPort()
    {
        return $this->get('port');
    }

    public function getUser()
    {
        return $this->get('user');
    }

    public function getPassword()
    {
        return $this->get('password');
    }

    public function getExchange()
    {
        return $this->get('exchange');
    }

    public function getType()
    {
        return $this->get('type');
    }

    public function getQueue()
    {
        return $this->get('queue');
    }

    public function isAutoAck(): bool
    {
        return (bool)$this->get('auto_ack',false);
    }
}