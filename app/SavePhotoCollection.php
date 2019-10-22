<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 9/30/2019
 * Time: 9:39 PM
 */

namespace App;


use phpDocumentor\Reflection\Types\Collection;

class SavePhotoCollection
{
    /**
     * @var SavePhoto
     */
    private $savePhoto;

    public function __construct(SavePhoto $savePhoto)
    {
        $this->savePhoto = $savePhoto;
    }

    public function save($photos)
    {
        return collect($photos)->map(function ($media) {
            $cropData = json_decode($media['cropData'], true);
            $cropData = empty($cropData) ? false : $cropData;

            return $this->savePhoto->save($media['file'], $cropData);
        });
    }

    public function update(array $photos)
    {
        collect($photos)->each(function ($media) {

            $this->savePhoto->update($media['id'],  $media['cropBoxData']);
        });
    }

    public function delete($deleted)
    {
        collect($deleted)->each(function ($id) {
            $this->savePhoto->delete($id);
        });
    }
}
