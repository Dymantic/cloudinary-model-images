<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Dymantic\CloudinaryModelImages\ImageUpload;
use Illuminate\Http\UploadedFile;

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
}