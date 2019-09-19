<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $appends = ['preview_url'];

    public function getPreviewUrlAttribute()
    {
        return Storage::url($this->path . "/" . $this->name . "." . $this->ext);
    }
}
