<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskExecutor extends Pivot
{
    protected $fillable = [
        'task_id',
        'executor_id',
        'status',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function executor()
    {
        return $this->belongsTo(User::class, 'executor_id');
    }
}
