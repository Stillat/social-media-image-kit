<?php

namespace Stillat\SocialMediaImageKit;

use Stillat\SocialMediaImageKit\Contracts\ImageNameFormatter;

class GeneratedImageNameFormatter implements ImageNameFormatter
{
    public function getImageName($entryId, $entry, array $size): string
    {
        return 'i'.$entryId.'-'.$size['handle'];
    }
}
