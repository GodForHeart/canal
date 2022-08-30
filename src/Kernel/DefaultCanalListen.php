<?php

namespace Godforheart\Canal\Kernel;

use Godforheart\Canal\Kernel\Contracts\CanalListen;

class DefaultCanalListen implements CanalListen
{
    public function handle(RowData $rowData)
    {
        var_dump(['DefaultCanalListen:handle' => $rowData->toJson()]);
//        var_dump($rowData->toJson());exit;
    }
}