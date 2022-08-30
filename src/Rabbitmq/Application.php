<?php

namespace Godforheart\Canal\Rabbitmq;

use Godforheart\Canal\Kernel\Contracts\Application as ApplicationInterface;
use Godforheart\Canal\Kernel\Traits\WithConfig;
use Godforheart\Canal\Kernel\Contracts\Service as ServiceInterface;

class Application implements ApplicationInterface
{
    use WithConfig;

    /**
     * @var ServiceInterface
     */
    private $service;

    public function getService(): Service
    {
        if (!$this->service) {
            $this->service = new Service($this->getConfig());
        }

        return $this->service;
    }
}