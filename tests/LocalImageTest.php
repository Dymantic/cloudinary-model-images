<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Dymantic\CloudinaryModelImages\LocalImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalImageTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpTestDisk();
    }

    /**
     *@test
     */
    public function create_a_local_image_from_uploaded_file_on_given_disk()
    {
        $file = UploadedFile::fake()->image('test.png');
        $local_image = new LocalImage($file, 'local-test-disk');

        Storage::disk('local-test-disk')->assertExists($local_image->path);
        $this->assertTrue(file_exists($local_image->full_path));
    }

    /**
     *@test
     */
    public function resize_with_options()
    {
        $file = UploadedFile::fake()->image('test.png', 400, 400);
        $local_image = new LocalImage($file, 'local-test-disk');

        $local_image->resize([
            'ratio' => '3:2',
            'max_width' => 120,
            'max_height' => 120
        ]);

        $this->assertImageSize($local_image->full_path, 120, 80);
    }

    /**
     *@test
     */
    public function tall_sizes_are_okay()
    {
        $file = UploadedFile::fake()->image('test.png', 100, 400);
        $local_image = new LocalImage($file, 'local-test-disk');

        $local_image->resize([
            'ratio' => '5:4',
            'max_width' => 120,
            'max_height' => 120
        ]);

        $this->assertImageSize($local_image->full_path, 100, 80);
    }

    /**
     *@test
     */
    public function wide_sizes_are_okay()
    {
        $file = UploadedFile::fake()->image('test.png', 400, 100);
        $local_image = new LocalImage($file, 'local-test-disk');

        $local_image->resize([
            'ratio' => '1:1',
            'max_width' => 120,
            'max_height' => 120
        ]);

        $this->assertImageSize($local_image->full_path, 100, 100);
    }

    /**
     *@test
     */
    public function small_sizes_are_okay()
    {
        $file = UploadedFile::fake()->image('test.png', 400, 400);
        $local_image = new LocalImage($file, 'local-test-disk');

        $local_image->resize([
            'ratio' => '4:3',
            'max_width' => 1200,
            'max_height' => 1200
        ]);

        $this->assertImageSize($local_image->full_path, 400, 300);
    }

    /**
     *@test
     */
    public function use_original_ratio_if_not_given()
    {
        $file = UploadedFile::fake()->image('test.png', 400, 300);
        $local_image = new LocalImage($file, 'local-test-disk');

        $local_image->resize([
            'max_width' => 120,
            'max_height' => 120
        ]);

        $this->assertImageSize($local_image->full_path, 120, 90);
    }

    private function assertImageSize($path, $expected_width, $expected_height)
    {
        $dimensions = getimagesize($path);
        $actual_width = $dimensions[0];
        $actual_height = $dimensions[1];

        $message = sprintf("expected [%s x %s] does not match actual [%s x %s]", $expected_width, $expected_height, $actual_width, $actual_height);

        $this->assertEquals($expected_height, $actual_height, $message);
        $this->assertEquals($expected_width, $actual_width, $message);
    }

    private function setUpTestDisk()
    {
        $disk_config = [
            'driver' => 'local',
            'root' => public_path('cloud_local'),
            'url' => 'https://localhost/cloud_local/',
            'visibility' => 'public',
        ];

        config(['filesystems.disks.local-test-disk' => $disk_config]);
        Storage::fake('local-test-disk', $disk_config);
    }
}