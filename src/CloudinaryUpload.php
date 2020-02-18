<?php


namespace Dymantic\CloudinaryModelImages;


class CloudinaryUpload
{
    public $public_id;
    public $cloud_name;
    public $version;
    public $url;
    public $type;
    public $format;

    public function __construct($data)
    {
        $this->public_id = $data['public_id'];
        $this->cloud_name = $data['cloud_name'];
        $this->version = $data['version'];
        $this->url = $data['url'];
        $this->type = $data['type'];
        $this->format = $data['format'];
    }

    public function asArray()
    {
        return [
            'public_id' => $this->public_id,
            'cloud_name' => $this->cloud_name,
            'version' => $this->version,
            'url' => $this->url,
            'type' => $this->type,
            'format' => $this->format,
        ];
    }
}