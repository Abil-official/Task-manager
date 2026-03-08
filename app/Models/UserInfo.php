<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInfo extends Model
{
    //
    protected $primaryKey = 'user_id';

    protected $incremental = false;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
