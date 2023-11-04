<?php

namespace Stillat\SocialMediaImageKit\Contracts;

use Stillat\SocialMediaImageKit\ImageDetails;

interface HtmlRenderer
{
    /**
     * Generates an image from the provided HTML content.
     *
     * @param  string  $path The local path to save the image.
     * @param  string  $html The HTML content.
     * @param  ImageDetails  $details The image details.
     */
    public function fromHtml(string $path, string $html, ImageDetails $details): bool;
}
