<?php

namespace Godforheart\Canal\Client;

use Godforheart\Canal\Kernel\Traits\WithConfig;
use Godforheart\Canal\Kernel\Contracts\Application as ApplicationInterface;
use Godforheart\Canal\Kernel\Contracts\Service as ServiceInterface;

class Application implements ApplicationInterface
{
    use WithConfig;

    /**
     * @var ServiceInterface
     */
    protected $service = null;

    public function getService()
    {
        if (!$this->service) {
            $this->service = new Service($this->getConfig());
        }

        return $this->service;
    }
}