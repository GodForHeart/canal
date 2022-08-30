<?php

namespace Godforheart\Canal\Client;

class Config extends \Godforheart\Canal\Kernel\Config
{
    protected $requiredKeys = [
        'type',
        'host',
        'port',
        'clientId',
        'destination',
        'filter',
    ];

    public function getType()
    {
        return $this->get('type');
    }

    public function getHost()
    {
        return $this->get('host');
    }

    public function getPort()
    {
        return $this->get('port');
    }

    public function getClientId()
    {
        return $this->get('clientId');
    }

    public function getDestination()
    {
        return $this->get('destination');
    }

    public function getFilter()
    {
        return $this->get('filter');
    }

    public function getNum()
    {
        return $this->get('num', 100);
    }
}