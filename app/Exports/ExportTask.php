<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;

class ExportTask implements FromQuery, ShouldQueue
{
    use Exportable;

    public function __construct() {}

    public function query()
    {
        return Task::query();
    }
}
