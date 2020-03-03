<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Dymantic\CloudinaryModelImages\CloudinaryImage;

class CloudinaryGetUrlTest extends TestCase
{
    /**
     *@test
     */
    public function gets_the_original_url_when_no_ratio()
    {
        $image = $this->makeImage();

        $this->assertEquals('https://res.cloudinary.com/test_cloud_name/image/upload/v123456789/test_public_id.jpg', $image->getUrl());
    }

    /**
     *@test
     */
    public function uses_ratio_if_saved()
    {
        $image = $this->makeImage();
        $image->ratio = "3:2";

        $this->assertEquals('https://res.cloudinary.com/test_cloud_name/image/upload/c_fill,ar_3:2/v123456789/test_public_id.jpg', $image->getUrl());
    }

    /**
     *@test
     */
    public function the_transforms_may_be_specified()
    {
        $image = $this->makeImage();
        $image->ratio = "3:2";

        $this->assertEquals('https://res.cloudinary.com/test_cloud_name/image/upload/c_fill,ar_3:2,w_1000,q_80/v123456789/test_public_id.jpg', $image->getUrl(['w_1000', 'q_80']));
    }

    /**
     *@test
     */
    public function default_transforms_can_be_overwritten()
    {
        $image = $this->makeImage();
        $image->ratio = "3:2";

        $this->assertEquals('https://res.cloudinary.com/test_cloud_name/image/upload/c_fill,ar_16:9,w_1000,q_80/v123456789/test_public_id.jpg', $image->getUrl(['ar_16:9', 'w_1000', 'q_80']));
    }

    /**
     *@test
     */
    public function image_with_no_ratio_can_still_be_take_transform_params()
    {
        $image = $this->makeImage();
        $image->ratio = null;

        $this->assertEquals('https://res.cloudinary.com/test_cloud_name/image/upload/c_fill,w_1000,q_80/v123456789/test_public_id.jpg', $image->getUrl(['w_1000', 'q_80']));
    }

    private function makeImage()
    {
        $model = TestModel::create();
        return CloudinaryImage::forceCreate([
            'cloudinaryable_id' => $model->id,
            'cloudinaryable_type' => TestModel::class,
            'public_id' => 'test_public_id',
            'version' => '123456789',
            'cloud_name' => 'test_cloud_name',
            'url' => 'https://res.cloudinary.com/test_cloud_name/image/upload/v123456789/test_public_id.jpg',
            'type' => 'image',
            'tag' => 'test-tag',
            'ratio' => null,
            'format' => 'jpg',
        ]);
    }
}