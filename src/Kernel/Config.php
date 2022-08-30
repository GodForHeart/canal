<?php

namespace Godforheart\Canal\Kernel;

use Godforheart\Canal\Kernel\Support\Arr;

class Config
{
    /**
     * @var array
     */
    private $items;

    protected $requiredKeys = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;

        $this->checkMissingKeys();
    }

    public function has(string $key): bool
    {
        return Arr::has($this->items, $key);
    }

    public function get(string $key, $default = null)
    {
        return Arr::get($this->items, $key, $default);
    }

    /**
     * @param string $key
     * @param mixed|null $value
     */
    public function set(string $key, $value = null): void
    {
        Arr::set($this->items, $key, $value);
    }

    /**
     * @return  array<string, mixed>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function offsetExists($key): bool
    {
        return $this->has(strval($key));
    }

    public function offsetGet($key)
    {
        return $this->get(strval($key));
    }

    public function offsetSet($key, $value): void
    {
        $this->set(strval($key), $value);
    }

    public function offsetUnset($key): void
    {
        $this->set(strval($key), null);
    }

    public function getDebugSingle(): bool
    {
        return (bool)$this->get('debug_single', false);
    }

    /**
     * @return bool
     * @throws \Exception
     * @author 潘琪焕
     */
    public function checkMissingKeys(): bool
    {
        if (empty($this->requiredKeys)) {
            return true;
        }

        $missingKeys = [];

        foreach ($this->requiredKeys as $key) {
            if (!$this->has($key)) {
                $missingKeys[] = $key;
            }
        }

        if (!empty($missingKeys)) {
            throw new \Exception(sprintf("\"%s\" cannot be empty.\r\n", join(',', $missingKeys)));
        }

        return true;
    }
}