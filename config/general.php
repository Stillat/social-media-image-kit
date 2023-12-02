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
    | Preview Enabled
    |--------------------------------------------------------------------------
    |
    | When enabled, the Social Media Image Kit will add a preview action button
    | to eligible entries in the Control Panel. This button will allow you to
    | preview the social media image for an entry for development purposes.
    |
    */

    'preview_enabled' => true,

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

    /*
    |--------------------------------------------------------------------------
    | Folder Template
    |--------------------------------------------------------------------------
    |
    | This option defines the template that is used to generate the folder
    | path for each image. The template is rendered using the Antlers
    | template engine. You should not change this setting after you
    | have generated images for your entries. Nested folders are
    | encouraged to help improve the Control Panel experience.
    |
    */

    'folder' => <<<'EOT'
{{ if date }}
   social-media/{{ date | format('Y') }}/{{ date | format ('m') }}/{{ date | format ('d') }}
{{ else }}
   social-media/{{ collection:handle /}}
{{ /if }}
EOT
    ,

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
