<?php

namespace Dymantic\CloudinaryModelImages;

use Illuminate\Support\ServiceProvider;

class CloudinaryModelImagesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (!class_exists('CreateCloudinaryModelImagesTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_cloudinary_model_images_table.php.stub' => database_path('migrations/' . date('Y_m_d_His',
                        time()) . '_create_cloudinary_model_images_table.php'),
            ], 'migrations');
        }

        $this->publishes([
            __DIR__ . '/../config/cloudinary-images.php' => config_path('cloudinary-images.php')
        ]);

        CloudinaryImage::observe(ImageObserver::class);
    }

    public function register()
    {
        if($this->shouldUseLocal()) {
            $this->app->bind(UploadClient::class, function() {
                return new LocalClient(
                    config('cloudinary-images.local_disk', config('filesystems.default'))
                );
            });
        } else {
            $this->app->bind(UploadClient::class, function() {
                return new CloudinaryClient([
                    'key' => config('cloudinary-images.key'),
                    'secret' => config('cloudinary-images.secret'),
                    'cloud_name' => config('cloudinary-images.cloud_name'),
                    'folder' => config('cloudinary-images.folder'),
                ]);
            });
        }


    }

    private function shouldUseLocal()
    {
        return config('cloudinary-images.use_local') && !($this->app->environment('production'));
    }

}
