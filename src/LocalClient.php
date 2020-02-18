<?php


namespace Dymantic\CloudinaryModelImages;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalClient implements UploadClient
{

    public $disk;

    public function __construct($disk)
    {
        $this->disk = $disk;
    }

    public function upload(UploadedFile $file, $options = []): CloudinaryUpload
    {
        $local_image = new LocalImage($file, $this->disk);
        $local_image->resize($options);


        return new CloudinaryUpload([
            'public_id' => $local_image->path,
            'url' => Storage::disk($this->disk)->url($local_image->path),
            'version' => 'local',
            'cloud_name' => $this->disk,
            'type' => 'image',
            'format' => $file->getClientOriginalExtension(),
        ]);
    }

    public function delete($image): bool
    {
        return Storage::disk($image->cloud_name)->delete($image->public_id);
    }
}