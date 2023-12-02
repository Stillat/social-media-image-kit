<?php

namespace Stillat\SocialMediaImageKit\Actions;

use Illuminate\Support\Collection;
use Statamic\Actions\Action;
use Statamic\Entries\Entry;
use Stillat\SocialMediaImageKit\Configuration;

class SocialPreviewTemplateAction extends Action
{
    protected $confirm = false;

    public static function title()
    {
        return 'Social Preview Template';
    }

    public function redirect($items, $values)
    {
        if (! $items instanceof Collection) {
            return;
        }

        $entry = $items->first();

        if (! $entry instanceof Entry) {
            return;
        }

        return route('statamic.cp.social-media-image-kit.preview', [
            'id' => $entry->id(),
        ]);
    }

    public function visibleTo($item)
    {
        if (! $item instanceof Entry) {
            return false;
        }

        $collection = $item->collection()?->handle();

        if ($collection === null || ! in_array($collection, Configuration::collections())) {
            return false;
        }

        return true;
    }

    public function visibleToBulk($items)
    {
        return false;
    }
}
