<?php

namespace Stillat\SocialMediaImageKit\Rendering;

use Spatie\Browsershot\Browsershot;
use Stillat\SocialMediaImageKit\Contracts\HtmlRenderer;
use Stillat\SocialMediaImageKit\ImageDetails;

class BrowsershotRenderer implements HtmlRenderer
{
    protected BrowsershotFactory $factory;

    public function __construct(BrowsershotFactory $factory)
    {
        $this->factory = $factory;
    }

    public function fromHtml(string $path, string $html, ImageDetails $details): bool
    {
        $browserhotInstance = Browsershot::html($html)
            ->windowSize($details->width, $details->height)
            ->waitUntilNetworkIdle();

        $this->factory->applyConfiguration($browserhotInstance);

        $browserhotInstance->save($path);

        return file_exists($path);
    }
}
