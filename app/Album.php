<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $guarded = ['id'];

    public function media()
    {
        return $this->belongsToMany(Media::class, 'media_album')
            ->withPivot('order')
            ->orderBy('media_album.order');
    }

    public function user()
    {
        $this->hasOne(User::class);
    }
}
