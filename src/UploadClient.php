<?php


namespace Dymantic\CloudinaryModelImages;


use Illuminate\Http\UploadedFile;

interface UploadClient
{
    public function upload(UploadedFile $file, $options = []): CloudinaryUpload;

    public function delete($image): bool ;
}