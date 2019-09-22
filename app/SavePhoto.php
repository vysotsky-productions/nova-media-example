<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 9/20/2019
 * Time: 5:11 PM
 */

namespace App;


use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SavePhoto
{

    protected $path;
    protected $config;

    public function __construct($path, $config)
    {

        $this->path = $path;
        $this->config = $config;
    }

    public function save($file, $cropData)
    {

        $filename = $file->hashName();

        Storage::disk('public')->putFileAs($this->path, $file, $filename);

        $this->handleCrop($filename, $cropData);

        $name = collect(explode('.', $filename))
            ->slice(0, -1)
            ->join('');
        $ext = collect(explode('.', $filename))->last();

        return Media::create([
            'name' => $name,
            'path' => $this->path,
            'ext' => $ext
        ]);

    }

    public function update($id, $cropData)
    {
        $media = Media::find($id);

        $filename = $media->name . "." . $media->ext;

        $this->deleteCroppedImages($filename);

        $this->handleCrop($filename, $cropData);

    }

    public function delete($id)
    {
        $media = Media::find($id);

        $filename = $media->name . "." . $media->ext;

        Storage::disk('public')->delete($this->path . "/" . $filename);

        $this->deleteCroppedImages($filename);

        $media->delete();

    }

    public function deleteCroppedImages($filename)
    {
        Storage::disk('public')->delete($this->path . '/cropped/' . $filename);

        foreach ($this->config as $thumbFolder => $thumbSize) {
            Storage::disk('public')->delete($this->path . '/thumbs/' . $thumbFolder . '/' . $filename);
        }
    }

    public function handleCrop($filename, $cropData = false)
    {
        $file = Storage::disk('public')->get($this->path . "/" . $filename);

        $cropped = $cropData ? Image::make($file)
            ->crop((int)$cropData->width, (int)$cropData->height, (int)$cropData->x, (int)$cropData->y)
            ->encode('jpeg', 100)
            : Image::make($file)->encode('jpeg', 100);

        Storage::disk('public')->put($this->path . '/cropped/' . $filename, $cropped);

        $image = $cropped;
        foreach ($this->config as $thumbFolder => $thumbSize) {

            if ($thumbSize['width'] && $thumbSize['height']) {
                $image->fit($thumbSize['width'], $thumbSize['height']);
            } else {
                $image->resize($thumbSize['width'], $thumbSize['height'], function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            Storage::disk('public')->put($this->path . '/thumbs/' . $thumbFolder . '/' . $filename, $image->encode('jpeg', 90));

        }

    }
}