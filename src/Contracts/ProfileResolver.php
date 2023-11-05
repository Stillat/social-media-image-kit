<?php

namespace Stillat\SocialMediaImageKit\Contracts;

interface ProfileResolver
{
    /**
     * Returns an array of supported social media profile sizes.
     */
    public function getSizes(): array;

    public function getSize($handle): ?array;
}
