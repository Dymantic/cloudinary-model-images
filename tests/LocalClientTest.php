<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Dymantic\CloudinaryModelImages\CloudinaryImage;
use Dymantic\CloudinaryModelImages\LocalClient;
use Dymantic\CloudinaryModelImages\LocalImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalClientTest extends TestCase
{
    /**
     *@test
     */
    public function local_upload_saves_to_configured_disk()
    {
        $disk_config = [
            'driver' => 'local',
            'root' => public_path('cloud_local'),
            'url' => 'https://localhost/cloud_local/',
            'visibility' => 'public',
        ];

        config(['filesystems.disks.local-test-disk' => $disk_config]);
        Storage::fake('local-test-disk', $disk_config);

        $image = UploadedFile::fake()->image('test.png');
        $client = new LocalClient('local-test-disk');
        $result = $client->upload($image, [
            'ratio' => '3:2',
            'max_height' => 800,
            'max_width' => 1200,
        ]);

        Storage::disk('local-test-disk')->assertExists($result->public_id);
        $this->assertEquals(Storage::disk('local-test-disk')->url($result->public_id), $result->url);
    }

    /**
     *@test
     */
    public function local_uploads_may_be_deleted()
    {
        $disk_config = [
            'driver' => 'local',
            'root' => public_path('cloud_local'),
            'url' => 'https://localhost/cloud_local/',
            'visibility' => 'public',
        ];

        config(['filesystems.disks.local-test-disk' => $disk_config]);
        Storage::fake('local-test-disk', $disk_config);
        $file = UploadedFile::fake()->image('test.png');
        $local_image = new LocalImage($file, 'local-test-disk');

        $model = TestModel::create();
        $image = CloudinaryImage::forceCreate([
            'cloudinaryable_id' => $model->id,
            'cloudinaryable_type' => TestModel::class,
            'public_id' => $local_image->path,
            'version' => 'local',
            'cloud_name' => 'local-test-disk',
            'url' => Storage::disk('local-test-disk')->url($local_image->path),
            'type' => 'image',
            'tag' => 'test-tag',
            'format' => 'jpg',
        ]);

        $client = new LocalClient('local-test-disk');
        $client->delete($image);

        Storage::disk('local-test-disk')->assertMissing($local_image->path);
    }
}