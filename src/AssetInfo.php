<?php

namespace Stillat\SocialMediaImageKit;

class AssetInfo
{
    public function __construct(
        public string $assetId,
        public string $imagePath,
        public string $assetContainer,
        public ?string $fileName
    ) {
    }
}
