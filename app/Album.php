<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $guarded = ['id'];

    public function media()
    {
        return $this->belongsToMany(Media::class, 'media_album');
    }

    public function user()
    {
        $this->hasOne(User::class);
    }
}
