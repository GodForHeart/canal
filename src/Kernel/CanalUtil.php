<?php

namespace Godforheart\Canal\Kernel;

use Com\Alibaba\Otter\Canal\Protocol\Column;
use Com\Alibaba\Otter\Canal\Protocol\EntryType;
use Com\Alibaba\Otter\Canal\Protocol\EventType;
use Com\Alibaba\Otter\Canal\Protocol\Header;
use Com\Alibaba\Otter\Canal\Protocol\RowChange;
use Com\Alibaba\Otter\Canal\Protocol\RowData;

class CanalUtil
{
    public function handleEntry(array $entries)
    {
        /** @var \Com\Alibaba\Otter\Canal\Protocol\Entry $entry */
        foreach ($entries as $entry) {
            $header = $entry->getHeader();
            if (in_array($entry->getEntryType(), [EntryType::TRANSACTIONBEGIN, EntryType::TRANSACTIONEND])) {
                continue;
            }

            $rowChange = new RowChange();
            $rowChange->mergeFromString($entry->getStoreValue());

            yield $this->handleRowChange($header, $rowChange);
        }
    }

    public function handleRowChange(Header $header, RowChange $rowChange)
    {
        $database = $header->getSchemaName();
        $table = $header->getTableName();
        $eventType = $rowChange->getEventType();

        $returnArray = [];
        /** @var RowData $rowData */
        foreach ($rowChange->getRowDatas() as $rowData) {
            $returnRow = $this->convertToRowData(
                $eventType,
                $database,
                $table,
                $this->getColumnArray($rowData->getBeforeColumns()),
                $this->getColumnArray($rowData->getAfterColumns())
            );
            $returnArray[] = $returnRow;
        }
        return array_filter($returnArray);
    }

    public function convertToRowData($eventType, $database, $table, array $beforeColumns, array $afterColumns): ?\Godforheart\Canal\Kernel\RowData
    {
        $beforeColumnArray = $afterColumnArray = [];
        $handleData = true;
        switch ($eventType) {
            case EventType::INSERT:
                $afterColumnArray = $afterColumns;
                break;
            case EventType::UPDATE:
                $beforeColumnArray = $beforeColumns;
                $afterColumnArray = $afterColumns;
                break;
            case EventType::DELETE:
                $beforeColumnArray = $beforeColumns;
                break;
            default:
                $handleData = false;
                break;
        }

        if (!$handleData) {
            return null;
        }

        $rowData = new \Godforheart\Canal\Kernel\RowData();
        $rowData->setEventType($eventType);
        $rowData->setDatabase($database);
        $rowData->setTable($table);
        $rowData->setBeforeArray($beforeColumnArray);
        $rowData->setAfterArray($afterColumnArray);

        return $rowData;
    }

    public function getColumnArray($columnArray): array
    {
        $returnArray = [];
        /** @var Column $column */
        foreach ($columnArray as $column) {
            $returnArray[] = [
                'index' => $column->getIndex(),
                'sql_type' => $column->getSqlType(),
                'name' => $column->getName(),
                'is_key' => $column->getIsKey(),
                'updated' => $column->getUpdated(),
                'is_null' => $column->getIsNull(),
                'props' => $column->getProps(),
                'value' => $column->getValue(),
                'length' => $column->getLength(),
                'mysql_type' => $column->getMysqlType(),
                'is_null_present' => $column->getIsNullPresent(),
            ];
        }
        return $returnArray;
    }
}