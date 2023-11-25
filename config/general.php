<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Social Media Image Generation Enabled
    |--------------------------------------------------------------------------
    |
    | The 'enabled' flag controls the automatic generation of social media images.
    | When set to false, the Social Media Image Kit will not generate images
    | when running commands or when specific events are fired. This option
    | is useful for disabling image generation in some environments.
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Asset Container
    |--------------------------------------------------------------------------
    |
    | This option defines the Statamic asset container utilized by the Social
    | Media Image Kit for image storage. Once this option is set, it should
    | not be changed. Doing so may result in broken images for entries.
    |
    */

    'asset_container' => 'social_media_images',

    'folder' => '',

    /*
    |--------------------------------------------------------------------------
    | Collection Handles for Image Generation
    |--------------------------------------------------------------------------
    |
    | The 'collections' array specifies which collection handles the Social Media
    | Image Kit addon will reference to retrieve entries. Only collections that
    | appear in this array will be used to generate social media images for.
    |
    */

    'collections' => [
        'pages',
        // Add more collections here.
    ],

];
