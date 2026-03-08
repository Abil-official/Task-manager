<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'task_id',
        'action',
        'description',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
