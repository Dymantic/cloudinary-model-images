<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Dymantic\CloudinaryModelImages\CloudinaryImage;
use Dymantic\CloudinaryModelImages\UploadClient;

class DeletingImagesTest extends TestCase
{
    /**
     *@test
     */
    public function delete_image_and_delete_on_cloudinary()
    {
        $model = TestModel::create();
        $image = CloudinaryImage::forceCreate([
            'cloudinaryable_id' => $model->id,
            'cloudinaryable_type' => TestModel::class,
            'public_id' => 'test_public_id',
            'version' => 'test_version',
            'cloud_name' => 'test_cloud_name',
            'url' => 'https://test.test',
            'type' => 'image',
            'tag' => 'test-tag',
            'format' => 'jpg',
        ]);
        $client = new TestClient();

        app()->instance(UploadClient::class, $client);

        $model->delete();
        $this->assertDatabaseMissing('cloudinary_model_images', ['id' => $image->id]);

        $client->assertDeleted($image);
    }
}