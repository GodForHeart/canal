<?php

namespace Godforheart\Canal\Kernel\Contracts;

use Godforheart\Canal\Kernel\Config;

interface Application
{
    public function getConfig(): Config;

    public function getService();
}