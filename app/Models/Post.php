<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'created_date',
    ];

    public function getCreatedDateAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }
}
