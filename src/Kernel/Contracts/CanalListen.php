<?php

namespace Godforheart\Canal\Kernel\Contracts;

use Godforheart\Canal\Kernel\RowData;

interface CanalListen
{
    public function handle(RowData $rowData);
}