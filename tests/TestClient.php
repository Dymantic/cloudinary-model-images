<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Dymantic\CloudinaryModelImages\CloudinaryImage;
use Dymantic\CloudinaryModelImages\CloudinaryUpload;
use Dymantic\CloudinaryModelImages\UploadClient;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Assert;

class TestClient implements UploadClient
{

    public $uploaded_files;
    public $deleted;

    public function __construct()
    {
        $this->uploaded_files = collect([]);
        $this->deleted = collect([]);
    }

    public function upload(UploadedFile $file, $options = []): CloudinaryUpload
    {
        $this->uploaded_files->push($file->getPath());
        return new CloudinaryUpload([
            'public_id' => 'test_public_id',
            'cloud_name' => 'test_cloud_name',
            'version' => 'abcde',
            'type' => 'image',
            'url' => 'https://test_url.test',
            'format' => $file->getClientOriginalExtension(),
        ]);
    }

    public function delete($image): bool
    {
        $this->deleted->push($image->public_id);
        return true;
    }

    public function assertUploaded($file)
    {
        Assert::assertTrue($this->uploaded_files->contains($file->getPath()));
    }

    public function assertDeleted($image)
    {
        Assert::assertTrue($this->deleted->contains($image->public_id));
    }
}