<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasUuids;

    protected $fillable = [
        'creator_id',
        'title',
        'description',
        'due_date',
    ];

    public function taskLogs(): HasMany
    {
        return $this->hasMany(TaskLog::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function executors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_executors', 'task_id', 'executor_id')->withPivot('status')->withTimestamps();
    }

    public function taskExecutors(): HasMany
    {
        return $this->hasMany(TaskExecutor::class);
    }
}
