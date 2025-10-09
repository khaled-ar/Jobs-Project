<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostsReport extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
        'image',
    ];

    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset("Images/PostsReports") . '/' . $this->image : null;
    }

    public function post() {
        return $this->belongsTo(Post::class)->select([
            'id', 'user_id', 'title_ar', 'whatsapp'
        ]);
    }

    public function user() {
        return $this->belongsTo(User::class)->select([
            'id', 'username', 'fcm'
        ]);
    }
}
