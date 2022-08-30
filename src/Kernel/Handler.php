<?php

namespace Godforheart\Canal\Kernel;

use Closure;

class Handler
{
    private $hash;
    private $handler;
    private $database = '';
    private $table = '';

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param mixed $handler
     * @return Handler
     */
    public function setHandler($handler)
    {
        $this->hash = $this->getHandlerHash($handler);
        $this->handler = $this->makeClosure($handler);
        return $this;
    }

    /**
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @param string $database
     * @return Handler
     */
    public function setDatabase(string $database): Handler
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     * @return Handler
     */
    public function setTable(string $table): Handler
    {
        $this->table = $table;
        return $this;
    }


    /**
     * @param $handler
     * @return string
     * @throws \Exception
     */
    protected function getHandlerHash($handler): string
    {
        if (is_string($handler)) {
            return $handler;
        } elseif (is_array($handler)) {
            return is_string($handler[0]) ?
                $handler[0] . '::' . $handler[1] : get_class($handler[0]) . $handler[1];
        } elseif ($handler instanceof Closure) {
            return spl_object_hash($handler);
        }
        throw new \Exception('Invalid handler: ' . gettype($handler));
    }

    protected function makeClosure($handler): callable
    {
        if (is_callable($handler)) {
            return $handler;
        }

        if (class_exists($handler)) {
            return function ($rowData) use ($handler) {
                return (new $handler())->handle($rowData);
            };
        }

        throw new \Exception(sprintf('Invalid handler: %s.', $handler));
    }
}