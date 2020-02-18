<?php

return [
    'key' => env('CLOUDINARY_KEY'),
    'secret' => env('CLOUDINARY_SECRET'),
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),

    // You may specify a folder to keep your cloudinary uploads in
    'folder' => null,

    // To save on your Cloudinary credits, you may use local storage when not
    // in production mode. Set use_local to true and specify which filesystem
    // disk to use, and images will be stored there when not in production
    'use_local' => false,
    'local_disk' => null,
];