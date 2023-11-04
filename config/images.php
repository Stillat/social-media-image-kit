<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Social Media Image Profiles Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration option outlines various profiles for social media images.
    | Each profile customizes the images to be suitable for specific social media
    | platforms, ensuring optimal display and integration. The fields within each
    | profile determine the unique properties and dimensions of the images.
    |
    | * `handle` - A unique identifier for the profile, used as a reference key.
    | * `name` - The human-friendly name for the profile, used for easy recognition.
    | * `width` - The width of the image in pixels, defining the horizontal size.
    | * `height` - The height of the image in pixels, setting the vertical size.
    | * `attributes` - An array of additional attributes for the image's HTML tag,
    |     such as 'name' or 'property' that may be required by social platforms.
    |
    */

    'profiles' => [
        [
            'handle' => 'twitter',
            'name' => 'Twitter',
            'width' => 1024,
            'height' => 512,
            'attributes' => [
                'name' => 'og:image',
            ],
        ],
        [
            'handle' => 'facebook',
            'name' => 'Facebook',
            'width' => 1200,
            'height' => 630,
            'attributes' => [
                'property' => 'og:image',
            ],
        ],
    ],

];
