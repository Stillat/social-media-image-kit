<?php

namespace Stillat\SocialMediaImageKit\Listeners;

use Statamic\Events\EntrySaved;
use Stillat\SocialMediaImageKit\Configuration;
use Stillat\SocialMediaImageKit\Jobs\GenerateSocialMediaImages;

class EntrySavedListener
{
    public function handle(EntrySaved $event): void
    {
        if (! Configuration::isEnabled() || ! Configuration::areEventsEnabled()) {
            return;
        }

        if (! in_array($event->entry?->collection()?->handle(), Configuration::collections())) {
            return;
        }

        GenerateSocialMediaImages::createJob($event->entry->id());
    }
}
