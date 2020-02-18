<?php


namespace Dymantic\CloudinaryModelImages;



use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

class LocalImage
{

    public $path;
    public $full_path;

    public function __construct(UploadedFile $file, $disk)
    {
        $this->path = $file->store('/images', $disk);
        $this->full_path = Storage::disk($disk)->path($this->path);
    }

    public function resize($options)
    {
        $dimensions = $this->imageDimensions();
        $ratio = $this->ratioAsFloat($options['ratio'] ?? false) ?? $dimensions['ratio'];
        $finalSize = $this->finalSize($options, $dimensions, $ratio);

        Image::load($this->full_path)
            ->fit(Manipulations::FIT_CROP, $finalSize['width'], $finalSize['height'])
            ->save();
    }

    private function ratioAsFloat($ratio_string)
    {
        if(!$ratio_string) {
            return null;
        }

        $parts = explode(":", $ratio_string);

        return $parts[0] / $parts[1];
    }

    private function imageDimensions()
    {
        $dims = getimagesize($this->full_path);

        return [
            'width' => $dims[0],
            'height' => $dims[1],
            'ratio' => $dims[0] / $dims[1],
        ];
    }

    private function finalSize($requested, $current, $ratio)
    {
        if(($requested['max_width'] >= $current['width']) && ($requested['max_height'] >= $current['height'])) {
            return $current['ratio'] >= 1 ?
                $this->sizeFromWidth($current['height'], $ratio) :
                $this->sizeFromHeight($current['width'], $ratio);
        }

        if(($requested['max_width'] <= $current['width']) && ($requested['max_height'] <= $current['height'])) {
            return $ratio >= 1 ?
                $this->sizeFromWidth($requested['max_height'], $ratio) :
                $this->sizeFromHeight($requested['max_width'], $ratio);
        }

        if($requested['max_width'] <= $current['width']) {
            return $this->sizeFromHeight($current['height'], $ratio);
        }

        return $this->sizeFromWidth($current['width'], $ratio);
    }

    private function sizeFromWidth($width, $ratio)
    {
        return [
            'width' => $width,
            'height' => intval($width / $ratio),
        ];
    }

    private function sizeFromHeight($height, $ratio)
    {
        return [
            'width' => intval($height * $ratio),
            'height' => $height,
        ];
    }
}