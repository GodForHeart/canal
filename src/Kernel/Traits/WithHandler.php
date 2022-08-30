<?php

namespace Godforheart\Canal\Kernel\Traits;

use Godforheart\Canal\Kernel\DefaultCanalListen;
use Godforheart\Canal\Kernel\Handler;
use Godforheart\Canal\Kernel\RowData;

trait WithHandler
{
    /**
     * 处理组
     * @var array
     */
    protected $handlers = [];

    protected $useDefaultHandler = true;

    public function getHandler(): array
    {
        if ($this->useDefaultHandler) {
            $defaultCanalListen = $this->config->get('default_canal_listen', DefaultCanalListen::class);
            return array_merge(
                [(new Handler())->setHandler($defaultCanalListen)],
                $this->handlers
            );
        }
        return $this->handlers;
    }


    public function handle(RowData $rowData)
    {
        if ($this->config->get('debug_database') && $rowData->getDatabase() != $this->config->get('debug_database')) {
            return;
        }
        if ($this->config->get('debug_table') && $rowData->getTable() != $this->config->get('debug_table')) {
            return;
        }

        /** @var Handler $item */
        foreach ($this->getHandler() as $item) {
            if ($item->getDatabase() && !preg_match("/^{$item->getDatabase()}$/", $rowData->getDatabase())) {
                continue;
            }
            if ($item->getTable() && !preg_match("/^{$item->getTable()}$/", $rowData->getTable())) {
                continue;
            }

            $item->getHandler()($rowData);
        }
    }

    public function addHandler(Handler $handler)
    {
        $this->handlers[] = $handler;

        return $this;
    }

    /**
     * @param $value
     * @param Handler $handler
     * @return $this|\Godforheart\Canal\Client\Service|\Godforheart\Canal\Rabbitmq\Service
     */
    public function when($value, Handler $handler)
    {
        if (is_callable($value)) {
            $value = call_user_func($value, $this);
        }

        if ($value) {
            return $this->addHandler($handler);
        }

        return $this;
    }

    public function withoutDefaultHandler()
    {
        $this->useDefaultHandler = false;
        return $this;
    }
}