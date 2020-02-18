<?php


namespace Dymantic\CloudinaryModelImages;


use Illuminate\Http\UploadedFile;

class ImageUpload
{
    public $image;
    public $model;
    public $ratio;
    public $max_width;
    public $max_height;

    public function __construct(UploadedFile $image, $model)
    {
        $this->image = $image;
        $this->model = $model;
    }

    public function withRatio($ratio)
    {
        $this->ratio = $ratio;
        return $this;
    }

    public function constrainedTo($max_width, $max_height = null)
    {
        $this->max_height = $max_height ?? $max_width;
        $this->max_width = $max_width;
        return $this;
    }

    public function saveAs($tag)
    {
        $upload = $this->upload();

        return $this->persistImageDetails($upload, $tag);
    }

    public function saveAsOnly($tag)
    {
        $existing = $this->model->cloudinaryImages()->where('tag', $tag)->get();

        $upload = $this->upload();

        $existing->each->delete();

        return $this->persistImageDetails($upload, $tag);
    }

    private function upload()
    {
        $client = app()->make(UploadClient::class);
        return $client->upload($this->image, [
            'ratio' => $this->ratio,
            'max_width' => $this->max_width,
            'max_height' => $this->max_height,
        ]);
    }

    private function persistImageDetails($upload, $tag)
    {
        return $this
            ->model
            ->cloudinaryImages()
            ->create(array_merge($upload->asArray(), ['tag' => $tag, 'ratio' => $this->ratio]));
    }
}