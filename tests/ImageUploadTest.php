<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Dymantic\CloudinaryModelImages\CloudinaryImage;
use Dymantic\CloudinaryModelImages\ImageUpload;
use Dymantic\CloudinaryModelImages\UploadClient;
use Illuminate\Http\UploadedFile;

class ImageUploadTest extends TestCase
{
    /**
     *@test
     */
    public function ratio_can_be_set_for_upload()
    {

        $upload = new ImageUpload(UploadedFile::fake()->image('test.png'), TestModel::create([]));

        $upload->withRatio("4:5");

        $this->assertEquals("4:5", $upload->ratio);
    }

    /**
     *@test
     */
    public function can_constrain_to_given_height_and_width()
    {
        $upload = new ImageUpload(UploadedFile::fake()->image('test.png'), TestModel::create([]));

        $upload->constrainedTo(1200, 600);

        $this->assertEquals(1200, $upload->max_width);
        $this->assertEquals(600, $upload->max_height);
    }

    /**
     *@test
     */
    public function constrain_to_square_by_only_passing_one_param()
    {
        $upload = new ImageUpload(UploadedFile::fake()->image('test.png'), TestModel::create([]));

        $upload->constrainedTo(888);

        $this->assertEquals(888, $upload->max_width);
        $this->assertEquals(888, $upload->max_height);
    }

    /**
     *@test
     */
    public function saves_with_the_correct_tag()
    {
        app()->bind(UploadClient::class, TestClient::class);
        $model = TestModel::create([]);

        $upload = new ImageUpload(UploadedFile::fake()->image('test.png'), $model);

        $upload->saveAs('test-tag');

        $this->assertDatabaseHas('cloudinary_model_images', [
            'cloudinaryable_id' => $model->id,
            'cloudinaryable_type' => TestModel::class,
            'tag' => 'test-tag',
        ]);
    }

    /**
     *@test
     */
    public function saves_as_the_only_instance_for_given_tag()
    {
        app()->bind(UploadClient::class, TestClient::class);
        $model = TestModel::create([]);

        $old = CloudinaryImage::forceCreate([
            'cloudinaryable_id' => $model->id,
            'cloudinaryable_type' => TestModel::class,
            'public_id' => 'old_public_id',
            'version' => 'test_version',
            'cloud_name' => 'test_cloud_name',
            'url' => 'https://test.test',
            'type' => 'image',
            'tag' => 'test-tag',
            'format' => 'jpg',
        ]);

        $upload = new ImageUpload(UploadedFile::fake()->image('test.png'), $model);

        $upload->saveAsOnly('test-tag');

        $this->assertDatabaseHas('cloudinary_model_images', [
            'cloudinaryable_id' => $model->id,
            'cloudinaryable_type' => TestModel::class,
            'public_id' => 'test_public_id',
            'tag' => 'test-tag',
        ]);

        $this->assertCount(1, $model->cloudinaryImages()->where('tag', 'test-tag')->get());
    }

    /**
     *@test
     */
    public function the_saved_cloudinary_image_has_the_ratio()
    {
        app()->bind(UploadClient::class, TestClient::class);
        $model = TestModel::create([]);

        $upload = new ImageUpload(UploadedFile::fake()->image('test.png'), $model);

        $upload->withRatio("3:2")->saveAs('test-tag');

        $this->assertDatabaseHas('cloudinary_model_images', [
            'cloudinaryable_id' => $model->id,
            'cloudinaryable_type' => TestModel::class,
            'tag' => 'test-tag',
            'ratio' => '3:2',
        ]);
    }
}