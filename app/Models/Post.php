<?php

namespace App\Models;

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

    public function getCreatedDateAttribute()
    {
        if($this->created_at->subDay() <= Carbon::yesterday())
            return $this->created_at->format('Y-m-d');
        return $this->created_at->diffForHumans();
    }

    protected static function booted()
    {
        static::retrieved(function ($post) {
            $locale = app()->getLocale();

            $post->setAttribute('gender', $post->gender == 'male' ? 'ذكر' : 'انثى');

            if(Route::currentRouteNamed('visitor.posts')) {
                $title = $post->title;
                $text = $post->text;

                if($locale == 'ar') {
                    $title = $post->title_ar;
                    $text = $post->text_ar;
                }

                $post->setAttribute('title', $title);
                $post->setAttribute('text', $text);

                unset($post->title_ar, $post->text_ar);
            }
        });
    }
}
