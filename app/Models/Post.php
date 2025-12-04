<?php

namespace App\Models;

use App\Services\GoogleTranslateService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

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

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function getCreatedDateAttribute()
    {
        $createdAt = $this->created_at;

        if ($createdAt->diffInHours() > 24) {
            return $createdAt->format('Y-m-d');
        }
        return $createdAt->diffForHumans();
        //return (new GoogleTranslateService())->translate($createdAt->diffForHumans(), app()->getLocale());
    }

    public function scopeStatus($query)
    {
        $status = request('status');
        if($status == 'pending') {
            return $query->whereStatus('pending');
        }
        return $query->whereStatus('active');
    }

    protected static function booted()
    {
        static::retrieved(function ($post) {
            $locale = app()->getLocale();

            if(Route::currentRouteNamed('visitor.posts')) {

                if($locale == 'ar') {
                    $gender = ($post->gender == 'male' || $post->gender == 'ذكر') ? 'ذكر' : 'انثى';
                } else {
                    $gender = ($post->gender == 'male' || $post->gender == 'ذكر') ? 'male' : 'female';
                }

                $post->setAttribute('gender', $gender);

            }
        });
    }
}
