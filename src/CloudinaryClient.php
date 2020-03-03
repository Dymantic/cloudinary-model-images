<?php


namespace Dymantic\CloudinaryModelImages;


use Cloudinary\Uploader;
use Illuminate\Http\UploadedFile;

class CloudinaryClient implements UploadClient
{

    private $key;
    private $secret;
    private $cloud_name;
    private $folder;

    public function __construct($attributes)
    {
         $this->key = $attributes['key'];
         $this->secret = $attributes['secret'];
         $this->cloud_name = $attributes['cloud_name'];
         $this->folder = $attributes['folder'];

        \Cloudinary::config([
            "cloud_name" => $this->cloud_name,
            "api_key" => $this->key,
            "api_secret" => $this->secret,
            "secure" => true
        ]);
    }

    public function upload(UploadedFile $file, $options = []): CloudinaryUpload
    {
        $cloud_options = [
            'width' => $options['max_width'] ?? 2400,
            'height' => $options['max_height'] ?? 2400,
            'crop' => 'limit',
        ];

        if($this->folder) {
            $cloud_options = array_merge($cloud_options, ['folder' => $this->folder]);
        }

        $response = Uploader::upload($file, $cloud_options);
        return new CloudinaryUpload([
            'public_id' => $response['public_id'],
            'version' => $response['version'],
            'type' => $response['resource_type'],
            'url' => $response['secure_url'],
            'format' => $response['format'],
            'cloud_name' => $this->cloud_name,
        ]);
    }

    public function delete($image): bool
    {
        $response = Uploader::destroy($image->public_id);
        $result = $response['result'] ?? 'failed';
        return $result === 'ok' ;
    }
}