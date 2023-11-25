<?php

namespace Stillat\SocialMediaImageKit;

use Statamic\Contracts\Entries\Entry;
use Statamic\Facades\Antlers;
use Stillat\SocialMediaImageKit\Contracts\FolderNameFormatter;

class AntlersFolderNameFormatter implements FolderNameFormatter
{
    public function getFolderName(Entry $entry): string
    {
        $configuredFolderName = trim(config('social_media_image_kit.general.folder', ''));

        if ($configuredFolderName == '') {
            return '';
        }

        $data = $entry->toAugmentedArray();

        return trim((string) Antlers::parse($configuredFolderName, $data));
    }
}
