<?php

namespace Stillat\SocialMediaImageKit;

use Stillat\SocialMediaImageKit\Contracts\ProfileResolver;

class ConfigProfilerResolver implements ProfileResolver
{
    protected string $configKey = 'social_media_image_kit.images.profiles';

    public function getSizes(): array
    {
        return config($this->configKey, []);
    }

    public function getSize($handle): ?array
    {
        foreach ($this->getSizes() as $profile) {
            if ($profile['handle'] === $handle) {
                return $profile;
            }
        }

        return null;
    }
}
