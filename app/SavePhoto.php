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
    public static function save($file, $cropData, $path, $config)
    {
        $filename = $file->hashName();


        $original = Storage::disk('public')->putFileAs($path, $file, $filename);

        $cropped = Image::make($file)
            ->crop((int)$cropData->width, (int)$cropData->height, (int)$cropData->x, (int)$cropData->y)
            ->encode('jpeg', 50);

        Storage::disk('public')->put($path . '/cropped/' . $filename, $cropped);

        $image = $cropped;
        foreach ($config as $thumbFolder => $thumbSize) {

            if ($thumbSize['width'] && $thumbSize['height']) {
                $image->fit($thumbSize['width'], $thumbSize['height']);
            } else {
                $image->resize($thumbSize['width'], $thumbSize['height'], function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            Storage::disk('public')->put($path . '/thumbs/' . $thumbFolder . '/' . $filename, $image->encode('jpg', 90));

        }

        $name = collect(explode('.', $filename))
            ->slice(0, -1)
            ->join('');
        $ext = collect(explode('.', $filename))->last();
        $newMedia = Media::create([
            'name' => $name,
            'path' => $path,
            'ext' => $ext
        ]);

        return $newMedia;

    }

    public static function update($id, $config, $path, $cropData)
    {
        $media = Media::find($id);

        $filename = $media->name . "." . $media->ext;
        self::deleteCroppedImages($config, $path, $filename);

        self::handleCrop($config, $path, $filename, $cropData);
    }

    public static function delete($id, $config, $path)
    {
        $media = Media::find($id);

        $filename = $media->name . "." . $media->ext;

        Storage::disk('public')->delete($path . "/" . $filename);

        self::deleteCroppedImages($config, $path, $filename);

        $media->delete();

    }

    public static function deleteCroppedImages($config, $path, $filename)
    {
        Storage::disk('public')->delete($path . '/cropped/' . $filename);

        foreach ($config as $thumbFolder => $thumbSize) {
            Storage::disk('public')->delete($path . '/thumbs/' . $thumbFolder . '/' . $filename);
        }
    }

    public static function handleCrop($config, $path, $filename, $cropData)
    {
        $file = Storage::disk('public')->get($path . "/" . $filename);


        $cropped = Image::make($file)
            ->crop((int)$cropData->width, (int)$cropData->height, (int)$cropData->x, (int)$cropData->y)
            ->encode('jpeg', 100);

        Storage::disk('public')->put($path . '/cropped/' . $filename, $cropped);

        $image = $cropped;
        foreach ($config as $thumbFolder => $thumbSize) {

            if ($thumbSize['width'] && $thumbSize['height']) {
                $image->fit($thumbSize['width'], $thumbSize['height']);
            } else {
                $image->resize($thumbSize['width'], $thumbSize['height'], function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            Storage::disk('public')->put($path . '/thumbs/' . $thumbFolder . '/' . $filename, $image->encode('jpg', 90));

        }

    }
}