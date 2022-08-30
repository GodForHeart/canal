<?php

namespace Godforheart\Canal\Kernel;

use EasyWeChat\Kernel\Contracts\Arrayable;

class RowData
{
    private $eventType;
    private $database;
    private $table;
    private $beforeArray = [];
    private $afterArray = [];

    /**
     * @return mixed
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @param mixed $eventType
     * @return RowData
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param mixed $database
     * @return RowData
     */
    public function setDatabase($database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     * @return RowData
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return array
     */
    public function getBeforeArray(): array
    {
        return $this->beforeArray;
    }

    /**
     * @param array $beforeArray
     * @return RowData
     */
    public function setBeforeArray(array $beforeArray): RowData
    {
        $this->beforeArray = $beforeArray;
        return $this;
    }

    /**
     * @return array
     */
    public function getAfterArray(): array
    {
        return $this->afterArray;
    }

    /**
     * @param array $afterArray
     * @return RowData
     */
    public function setAfterArray(array $afterArray): RowData
    {
        $this->afterArray = $afterArray;
        return $this;
    }


    /**
     * 所有属性转换为数组
     * @return array
     * @author 潘琪焕
     */
    public function toArray(): array
    {
        $originalData = (new \ArrayObject($this))->getArrayCopy();
        $data = [];
        foreach ($originalData as $key => $val) {
            $newKey = preg_replace('/^\\x00.*\\x00/', '', $key);
            $data[$newKey] = $val;
        }
        return $data;
    }

    /**
     * 转换为json数据
     * @param int $options
     * @return false|string
     * @author 潘琪焕
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * 拷贝一份相同的过滤器
     * @return $this
     */
    public function copy()
    {
        return clone $this;
    }
}