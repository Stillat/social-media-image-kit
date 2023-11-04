<?php

namespace Stillat\SocialMediaImageKit\Listeners;

use Statamic\Events\EntryCreated;
use Stillat\SocialMediaImageKit\Configuration;
use Stillat\SocialMediaImageKit\Jobs\GenerateSocialMediaImages;

class EntryCreatedListener
{
    public function handle(EntryCreated $event): void
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
