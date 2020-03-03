<?php


namespace Dymantic\CloudinaryModelImages;


use Illuminate\Database\Eloquent\Model;

class CloudinaryImage extends Model
{
    protected $table = 'cloudinary_model_images';

    protected $fillable = [
        'public_id',
        'cloud_name',
        'version',
        'url',
        'type',
        'tag',
        'ratio',
        'format',
    ];

    public function cloudinaryable()
    {
        return $this->morphTo();
    }

    public function getUrl($transforms = [])
    {
        if(!$this->requiresTransforms($transforms) || $this->isLocal()) {
            return $this->url;
        }

        $format = "https://res.cloudinary.com/%s/%s/upload/%s/v%s/%s.%s";

        return sprintf(
            $format,
            $this->cloud_name,
            $this->type,
            $this->getTransforms($transforms),
            $this->version,
            $this->public_id,
            $this->format
        );
    }

    private function requiresTransforms($transforms = [])
    {
        return (!!$this->ratio) || count($transforms) > 0;
    }

    private function isLocal()
    {
        return $this->version === 'local';
    }

    private function getTransforms($transforms)
    {
        $defaults = [
            "c" => "fill",
        ];

        if($this->ratio) {
            $defaults = array_merge($defaults, ['ar' => $this->ratio]);
        }

        $given = collect($transforms)
            ->flatMap(function($transform) {
                $parts = explode("_", $transform);
                return [$parts[0] => $parts[1]];
            })
            ->all();

        return collect(array_merge($defaults, $given))
            ->map(function($value, $key) {
                return "{$key}_{$value}";
            })->join(",");
    }
}