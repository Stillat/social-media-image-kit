<?php

namespace Stillat\SocialMediaImageKit;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Statamic\Events\EntryCreated;
use Statamic\Events\EntrySaved;
use Statamic\Providers\AddonServiceProvider;
use Stillat\SocialMediaImageKit\Actions\SocialPreviewTemplateAction;
use Stillat\SocialMediaImageKit\Contracts\FolderNameFormatter;
use Stillat\SocialMediaImageKit\Contracts\HtmlRenderer;
use Stillat\SocialMediaImageKit\Contracts\ImageGenerator as ImageGeneratorContract;
use Stillat\SocialMediaImageKit\Contracts\ImageNameFormatter;
use Stillat\SocialMediaImageKit\Contracts\ProfileResolver;
use Stillat\SocialMediaImageKit\Fieldtypes\SocialMediaImageType;
use Stillat\SocialMediaImageKit\Rendering\BrowsershotFactory;
use Stillat\SocialMediaImageKit\Rendering\BrowsershotRenderer;

class ServiceProvider extends AddonServiceProvider
{
    protected $tags = [
        Tags\GetSocialMediaImages::class,
    ];

    protected $fieldtypes = [
        SocialMediaImageType::class,
    ];

    protected $commands = [
        Console\CreateAssetContainer::class,
        Console\GenerateImages::class,
        Console\ImageTasksTable::class,
    ];

    protected $vite = [
        'input' => [
            'resources/js/addon.js',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    protected $listen = [
        EntryCreated::class => [
            Listeners\EntryCreatedListener::class,
        ],
        EntrySaved::class => [
            Listeners\EntrySavedListener::class,
        ],
    ];

    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/general.php', 'social_media_image_kit.general');
        $this->mergeConfigFrom(__DIR__.'/../config/generation.php', 'social_media_image_kit.generation');
        $this->mergeConfigFrom(__DIR__.'/../config/images.php', 'social_media_image_kit.images');
        $this->mergeConfigFrom(__DIR__.'/../config/queue.php', 'social_media_image_kit.queue');
        $this->mergeConfigFrom(__DIR__.'/../config/fields.php', 'social_media_image_kit.fields');

        $this->registerImageGenerator();

        $requiredConfigurationFiles = [
            __DIR__.'/../config/general.php' => config_path('social_media_image_kit/general.php'),
            __DIR__.'/../config/generation.php' => config_path('social_media_image_kit/generation.php'),
            __DIR__.'/../config/images.php' => config_path('social_media_image_kit/images.php'),
            __DIR__.'/../config/queue.php' => config_path('social_media_image_kit/queue.php'),
        ];

        // Ensure that the required configuration files exist. This reduces
        // having an extra step in the installation/onboarding process.
        foreach ($requiredConfigurationFiles as $addonVersion => $appVersion) {
            if (! file_exists($appVersion)) {
                @copy($addonVersion, $appVersion);
            }
        }

        $this->publishes(array_merge($requiredConfigurationFiles, [
            __DIR__.'/../config/fields.php' => config_path('social_media_image_kit/fields.php'),
        ]), 'social-media-image-kit-config');
    }

    public function bootAddon()
    {
        // Create temporary directory, if it doesn't exist.
        $kitTmpPath = config('social_media_image_kit.generation.tmp_path', storage_path('social-media-image-kit'));

        if (! file_exists($kitTmpPath)) {
            mkdir($kitTmpPath, 0755, true);
        }

        // Create a default template, if it doesn't exist.
        $defaultAntlers = Str::finish(config('social_media_image_kit.generation.template_path'), '/').'_default.antlers.html';
        $defaultBlade = Str::finish(config('social_media_image_kit.generation.template_path'), '/').'_default.blade.php';

        // Check if either default template option exists. We only want to
        // create one if neither exist, so we don't become a nuisance.
        if (! file_exists($defaultAntlers) && ! file_exists($defaultBlade)) {
            $dir = config('social_media_image_kit.generation.template_path');

            if (! file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            // Not the prettiest default, but it will do.
            file_put_contents($defaultAntlers, '{{ title }}');
        }

        $this->bootImagePreviewer();
    }

    protected function bootImagePreviewer()
    {
        if (config('social_media_image_kit.general.preview_enabled', false)) {
            View::addNamespace('social-media-image-kit', __DIR__.'/../resources/views');
            SocialPreviewTemplateAction::register();
        }
    }

    protected function registerImageGenerator(): void
    {
        $this->app->bind(ImageNameFormatter::class, GeneratedImageNameFormatter::class);
        $this->app->bind(FolderNameFormatter::class, AntlersFolderNameFormatter::class);

        $this->app->singleton(GeneratorFieldConfiguration::class, function () {
            return new GeneratorFieldConfiguration(
                assetContainer: config('social_media_image_kit.general.asset_container', 'social_media_images'),
                imagesFieldName: config('social_media_image_kit.fields.field_configuration.images_field', 'social_media_images'),
                assetFieldName: config('social_media_image_kit.fields.field_configuration.assets_field', 'asset_social_media_image'),
                preservedFieldName: config('social_media_image_kit.fields.field_configuration.preserve_field', 'preserve_image'),
                socialMediaPlatformType: config('social_media_image_kit.fields.field_configuration.social_media_type_field', 'social_media_image_type')
            );
        });

        $this->app->singleton(ProfileResolver::class, function () {
            return new ConfigProfilerResolver();
        });

        $this->app->singleton(BrowsershotFactory::class, function () {
            return new BrowsershotFactory();
        });

        $this->app->bind(HtmlRenderer::class, config('social_media_image_kit.generation.html_renderer', BrowsershotRenderer::class));
        $this->app->bind(ImageGeneratorContract::class, config('social_media_image_kit.generation.generator', ImageGenerator::class));

        $this->app->afterResolving(ImageGeneratorContract::class, function (ImageGeneratorContract $generator) {
            $generator->setImageExtension(
                config('social_media_image_kit.generation.image_format.extension', 'png')
            );

            $generator->setCleanupFiles(config('social_media_image_kit.generation.cleanup_files', true));
            $generator->setTmpDirectory(config('social_media_image_kit.generation.tmp_path', storage_path('app/social-media-image-kit')));
            $generator->setTemplatePath(config('social_media_image_kit.generation.template_path', resource_path('views/social-media-image-kit')));
        });
    }
}
