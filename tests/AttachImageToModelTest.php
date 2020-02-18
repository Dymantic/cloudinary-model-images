<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Dymantic\CloudinaryModelImages\CloudinaryImage;
use Dymantic\CloudinaryModelImages\UploadClient;
use Illuminate\Http\UploadedFile;

class AttachImageToModelTest extends TestCase
{
    /**
     * @test
     */
    public function attach_an_image_to_a_model()
    {
        $model = TestModel::create([]);
        $client = new TestClient();
        $file = UploadedFile::fake()->image('testpic.png');

        app()->instance(UploadClient::class, $client);

        $image = $model->attachImage($file)
                       ->saveAs('test-tag');


        $client->assertUploaded($file);

        $this->assertInstanceOf(CloudinaryImage::class, $image);
        $this->assertCount(1, $model->getCloudinaryImages('test-tag'));
        $this->assertDatabaseHas('cloudinary_model_images', ['tag' => 'test-tag']);
    }
}