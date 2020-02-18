<?php


namespace Dymantic\CloudinaryModelImages\Tests;



use Dymantic\CloudinaryModelImages\CloudinaryImage;
use Dymantic\CloudinaryModelImages\LocalImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalGetUrlTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('local-test-disk');
    }
    /**
     *@test
     */
    public function returns_the_url_for_the_image_from_local_disk()
    {
        $image = $this->makeImage();

        $this->assertEquals($image->url, $image->getUrl());
    }

    /**
     *@test
     */
    public function ignores_any_passed_params_for_local_images()
    {
        $image = $this->makeImage();

        $this->assertEquals($image->url, $image->getUrl(['w_1000', '0_50']));
    }

    private function makeImage()
    {
        $image = new LocalImage(UploadedFile::fake()->image('test.png'), 'local-test-disk');
        $model = TestModel::create();
        return CloudinaryImage::forceCreate([
            'cloudinaryable_id' => $model->id,
            'cloudinaryable_type' => TestModel::class,
            'public_id' => $image->path,
            'version' => 'local',
            'cloud_name' => 'local-test-disk',
            'url' => Storage::disk('local-test-disk')->url($image->path),
            'type' => 'image',
            'tag' => 'test-tag',
            'ratio' => "3:2",
            'format' => 'jpg',
        ]);
    }
}