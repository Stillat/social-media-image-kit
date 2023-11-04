<?php

namespace Stillat\SocialMediaImageKit\Events;

use Illuminate\Foundation\Events\Dispatchable;

class GeneratedImage
{
    use Dispatchable;

    public function __construct(
        public readonly object $entry,
        public readonly array $size
    ) {
    }
}
