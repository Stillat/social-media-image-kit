<?php

namespace Stillat\SocialMediaImageKit;

class GeneratorFieldConfiguration
{
    public function __construct(
        public readonly string $assetContainer = 'social_media_images',
        public readonly string $imagesFieldName = 'social_media_images',
        public readonly string $assetFieldName = 'asset_social_media_image',
        public readonly string $preservedFieldName = 'preserve_image',
        public readonly string $socialMediaPlatformType = 'social_media_image_type',
    ) {
    }
}
