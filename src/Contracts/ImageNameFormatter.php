<?php

namespace Stillat\SocialMediaImageKit\Contracts;

interface ImageNameFormatter
{
    public function getImageName($entryId, $entry, array $size): string;
}
