<?php


namespace Dymantic\CloudinaryModelImages;


use Illuminate\Http\UploadedFile;

trait CloudinaryImages
{

    public static function bootCloudinaryImages()
    {
        static::deleting(function($model) {
           $model->cloudinaryImages->each->delete();
        });
    }

    public function attachImage(UploadedFile $image)
    {
        return new ImageUpload($image, $this);
    }

    public function cloudinaryImages()
    {
        return $this->morphMany(CloudinaryImage::class, 'cloudinaryable');
    }

    public function getCloudinaryImages($tag = '')
    {
        return $this->cloudinaryImages()->where('tag', $tag)->get();
    }
}