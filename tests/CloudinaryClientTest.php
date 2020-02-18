<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Cloudinary\Uploader;
use Dymantic\CloudinaryModelImages\CloudinaryClient;
use Dymantic\CloudinaryModelImages\CloudinaryImage;
use Dymantic\CloudinaryModelImages\CloudinaryUpload;
use Illuminate\Http\UploadedFile;
use Mockery;

class CloudinaryClientTest extends TestCase
{
    /**
     *@test
     */
    public function upload_the_given_file()
    {
        $image = UploadedFile::fake()->image('test.png', 2000, 1000);
        $expected_options = [
            'width' => 1200,
            'height' => 800,
            'crop' => 'limit',
            'folder' => 'test-folder',
        ];

        $uploader = Mockery::mock("alias:". Uploader::class);
        $uploader->shouldReceive('upload')
                 ->once()
                 ->with($image, $expected_options)
                 ->andReturn([
                     "public_id"         => "test-folder/hzwo4p7hfdteoc1lr9ol",
                     "version"           => 1576901166,
                     "signature"         => "0e1bac580adfcec59a678eeee556de62979a49d4",
                     "width"             => 1200,
                     "height"            => 800,
                     "format"            => "png",
                     "resource_type"     => "image",
                     "created_at"        => "2019-12-21T04:06:06Z",
                     "tags"              => [],
                     "bytes"             => 411,
                     "type"              => "upload",
                     "etag"              => "b5fc08a647ae6db3dc34bdef57e5a8ba",
                     "placeholder"       => false,
                     "url"               => "http://res.cloudinary.com/dymanticdesign/image/upload/v1576901166/test/hzwo4p7hfdteoc1lr9ol.png",
                     "secure_url"        => "https://res.cloudinary.com/dymanticdesign/image/upload/v1576901166/test/hzwo4p7hfdteoc1lr9ol.png",
                     "original_filename" => "php25DPBG",
                 ]);

        $client = new CloudinaryClient([
            'key' => 'test_key',
            'secret' => 'test_secret',
            'cloud_name' => 'test_cloud',
            'folder' => 'test-folder',
        ]);

        $result = $client->upload($image, [
            'ratio' => '3:2',
            'max_height' => 800,
            'max_width' => 1200,
        ]);

        $this->assertInstanceOf(CloudinaryUpload::class, $result);
        $this->assertEquals('test-folder/hzwo4p7hfdteoc1lr9ol', $result->public_id);
        $this->assertEquals('1576901166', $result->version);
        $this->assertEquals('image', $result->type);
        $this->assertEquals('https://res.cloudinary.com/dymanticdesign/image/upload/v1576901166/test/hzwo4p7hfdteoc1lr9ol.png', $result->url);
        $this->assertEquals('test_cloud', $result->cloud_name);
    }

    /**
     *@test
     */
    public function delete_an_image()
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

        $uploader = Mockery::mock("alias:". Uploader::class);
        $uploader->shouldReceive('destroy')
                 ->once()
                 ->with($image->public_id)
                 ->andReturn(['result' => 'ok']);

        $client = new CloudinaryClient([
            'key' => 'test_key',
            'secret' => 'test_secret',
            'cloud_name' => 'test_cloud',
            'folder' => 'test-folder',
        ]);

        $result = $client->delete($image);

        $this->assertTrue($result);

    }
}