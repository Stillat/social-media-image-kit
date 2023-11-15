<?php

namespace Stillat\SocialMediaImageKit;

class Configuration
{
    public static function isEnabled(): bool
    {
        return config('social_media_image_kit.general.enabled', true);
    }

    public static function htmlRenderer(): ?string
    {
        return config('social_media_image_kit.generation.html_renderer', null);
    }

    public static function areEventsEnabled(): bool
    {
        return config('social_media_image_kit.queue.events_enabled', true);
    }

    public static function assetContainer(): string
    {
        return config('social_media_image_kit.general.asset_container', 'social_media_images');
    }

    public static function collections(): array
    {
        return config('social_media_image_kit.general.collections', []);
    }

    public static function preventDuplicateJobs(): bool
    {
        return config('social_media_image_kit.queue.prevent_duplicate_jobs', false);
    }
}
