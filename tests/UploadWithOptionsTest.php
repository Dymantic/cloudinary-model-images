<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Dymantic\CloudinaryModelImages\CloudinaryUpload;
use Dymantic\CloudinaryModelImages\UploadClient;
use Illuminate\Http\UploadedFile;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class UploadWithOptionsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @test
     */
    public function correct_upload_options_are_passed_to_client()
    {
        $client = \Mockery::mock('client');
        app()->instance(UploadClient::class, $client);

        $expected_options = [
            'ratio'      => '3:2',
            'max_width'  => 1200,
            'max_height' => 1200
        ];

        $client->shouldReceive('upload')
               ->with(\Mockery::type(UploadedFile::class), $expected_options)
               ->once()
               ->andReturn(new CloudinaryUpload([
                   'public_id'  => 'test_public_id',
                   'version'    => 'test_version',
                   'cloud_name' => 'test_cloud_name',
                   'url'        => 'https://test.test',
                   'type'       => 'image',
                   'format'     => 'jpg',
               ]));

        $model = TestModel::create([]);
        $model->attachImage(UploadedFile::fake()->image('testpic.jpg'))
              ->withRatio("3:2")
              ->constrainedTo(1200)
              ->saveAs('test-tag');
    }
}