<?php

namespace Stillat\SocialMediaImageKit\Contracts;

use Statamic\Contracts\Entries\Entry;

interface FolderNameFormatter
{
    public function getFolderName(Entry $entry): string;
}
