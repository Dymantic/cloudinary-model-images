<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Dymantic\CloudinaryModelImages\CloudinaryModelImagesServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as TestBench;

class TestCase extends TestBench
{

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    public function setUpDatabase($app) {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->nullableTimestamps();
        });

        include_once __DIR__ . '/../database/migrations/create_cloudinary_model_images_table.php.stub';

        (new \CreateCloudinaryModelImagesTable)->up();
    }

    protected function getPackageProviders($app)
    {
        return [
            CloudinaryModelImagesServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}