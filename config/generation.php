<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HTML Renderer Configuration
    |--------------------------------------------------------------------------
    |
    | Which HTML rendering engine should be used to generate the social media
    | images. By default, the Social Media Image Kit does not ship with a
    | renderer. You must install or create your own renderer. For more
    | information, please see the following documentation page:
    |
    */

    'html_renderer' => \Stillat\SocialMediaImageKit\Rendering\BrowsershotRenderer::class,

    /*
    |--------------------------------------------------------------------------
    | Image Generator
    |--------------------------------------------------------------------------
    |
    | Image generators are responsible for creating the social media images
    | using the configured HTML renderer, managing created images, and
    | keeping your entry's assets up-to-date. You may customize the
    | generator implementation used by the Social Media Image Kit.
    |
    */

    'generator' => \Stillat\SocialMediaImageKit\ImageGenerator::class,

    /*
    |--------------------------------------------------------------------------
    | Temporary Path for Image Processing
    |--------------------------------------------------------------------------
    |
    | A temporary path the Social Media Image Kit can use to store images during
    | processing. This path should be writable and accessible to the addon.
    | Images will be deleted from this path after they are processed and
    | converted into Statamic assets. You should add this path to your
    | .gitignore file to prevent the storage of temporary images.
    |
    */

    'tmp_path' => storage_path('app/social-media-image-kit'),

    /*
    |--------------------------------------------------------------------------
    | Cleanup Temporary Files After Processing
    |--------------------------------------------------------------------------
    |
    | When set to true, Social Media Image Kit will attempt to delete temporary
    | files after they have been processed. This is useful to keep the file
    | system clean and efficient. If you are experiencing issues with the
    | addon, you can disable this setting to inspect the files created.
    |
    */

    'cleanup_files' => true,

    /*
    |--------------------------------------------------------------------------
    | Template Path for Image Rendering
    |--------------------------------------------------------------------------
    |
    | The 'Template Path' setting specifies the directory location where the addon
    | will search for Antlers or Blade templates. Templates are used to create
    | the HTML that is supplied to the HTML renderer to generate images.
    |
    */

    'template_path' => resource_path('views/social-media-image-kit'),

    /*
    |--------------------------------------------------------------------------
    | Image Format Specification
    |--------------------------------------------------------------------------
    |
    | This configuration is utilized to set the 'extension' of each generated
    | image. By default, the Social Media Image Kit will generate PNG images
    | for each profile. The mime type will be set automatically for you.
    |
    */

    'image_format' => [
        'extension' => 'png',
    ],

];
