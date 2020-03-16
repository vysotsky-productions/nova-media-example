<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['preview_url', 'original_url', 'full_path', 'download_link'];
    protected $casts = [
        'crop_data' => 'array'
    ];

    public function albums()
    {
        $this->belongsToMany(Album::class, 'media_album');
    }

    public function getPreviewUrlAttribute()
    {
        return Storage::url($this->path . "/thumbs/576/" . $this->name . "." . $this->ext);
    }
    public function getOriginalUrlAttribute()
    {
        return Storage::url($this->path . "/" . $this->name . "." . $this->ext);
    }
    public function getFullPathAttribute()
    {
        return $this->path . "/" . $this->name . "." . $this->ext;
    }

    public function getDownloadLinkAttribute()
    {
        return Storage::url($this->path . "/cropped/" . $this->name . "." . $this->ext);
    }
}
