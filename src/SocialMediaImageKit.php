<?php

namespace Stillat\SocialMediaImageKit;

use Closure;
use Stillat\SocialMediaImageKit\Rendering\BrowsershotFactory;

class SocialMediaImageKit
{
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
        $browserhotFactory = app(Rendering\BrowsershotFactory::class);

        $browserhotFactory->configureWith($callback);
    }
}
