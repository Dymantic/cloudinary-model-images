<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Dymantic\CloudinaryModelImages\CloudinaryClient;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class CloudinaryClientConfigTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     *@test
     */
    public function it_sets_up_config_when_instantiated()
    {
        $mock = Mockery::mock("alias:" . \Cloudinary::class);
        $mock->shouldReceive('config')
             ->once()
             ->with(Mockery::on(function($arg) {
                 return $arg['api_key'] === 'test_key' &&
                     $arg['api_secret'] === 'test_secret' &&
                     $arg['cloud_name'] === 'test_cloud' &&
                     $arg['secure'] === true;
             }));

        $client = new CloudinaryClient([
            'key' => 'test_key',
            'secret' => 'test_secret',
            'cloud_name' => 'test_cloud',
            'folder' => 'test-folder',
        ]);

    }
}