<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Dymantic\CloudinaryModelImages\ImageUpload;
use Dymantic\CloudinaryModelImages\UploadClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

class CloudinaryImagesTest extends TestCase
{
    /**
     *@test
     */
    public function can_attach_an_image()
    {
        $model = TestModel::create([]);
        $image = UploadedFile::fake()->image('test.png');

        $upload = $model->attachImage($image);

        $this->assertInstanceOf(ImageUpload::class, $upload);
        $this->assertEquals($image, $upload->image);
        $this->assertEquals($model, $upload->model);
    }

    /**
     *@test
     */
    public function can_get_latest_image_of_tag()
    {
        app()->bind(UploadClient::class, TestClient::class);
        $model = TestModel::create([]);
        $imageA = UploadedFile::fake()->image('test.png');
        $imageB = UploadedFile::fake()->image('test_two.png');

        $uploadA = $model->attachImage($imageA)->saveAs('test-tag');
        $uploadA->created_at = Carbon::now()->subMinutes(2);
        $uploadA->save();
        $uploadB = $model->attachImage($imageB)->saveAs('test-tag');

        $first = $model->latestCloudinaryImage('test-tag');

        $this->assertEquals($uploadB->fresh(), $first);
    }

    
}