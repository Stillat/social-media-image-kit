<?php

namespace Stillat\SocialMediaImageKit\Support\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use Statamic\Contracts\Entries\Entry;
use Stillat\SocialMediaImageKit\ImageManager;
use Stillat\SocialMediaImageKit\Rendering\BrowsershotFactory;

/**
 * @method static bool entryHasProfile(Entry|string $entry, string $profile)
 * @method static void setProfileImage(Entry|string $entry, string $profile, string $filePath, bool $newPreserveSetting = false)
 */
class SocialMediaImageKit extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ImageManager::class;
    }

    /**
     * Registers a custom callback that can be used
     * to configure the Browsershot instance.
     *
     * @param  Closure  $callback The configuration callback.
     * @return void
     */
    public static function configureBrowsershot(Closure $callback)
    {
        /** @var BrowsershotFactory $browserhotFactory */
        $browserhotFactory = app(BrowsershotFactory::class);

        $browserhotFactory->configureWith($callback);
    }
}
