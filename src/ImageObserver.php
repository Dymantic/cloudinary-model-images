<?php


namespace Dymantic\CloudinaryModelImages;


class ImageObserver
{
    public function deleted(CloudinaryImage $image)
    {
        $client = app(UploadClient::class);

        $client->delete($image);
    }
}