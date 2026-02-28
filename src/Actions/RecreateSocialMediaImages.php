<?php

namespace Stillat\SocialMediaImageKit\Actions;

use Statamic\Actions\Action;
use Statamic\Entries\Entry;
use Stillat\SocialMediaImageKit\Configuration;
use Stillat\SocialMediaImageKit\Jobs\GenerateSocialMediaImages;

class RecreateSocialMediaImages extends Action
{
    protected $confirm = false;

    public static function title()
    {
        return 'Re-create Social Media Images';
    }

    /**
     * Determine if an item's collection is valid for this action.
     *
     * @param Entry $item
     * @return bool
     */
    private function isValidCollection(Entry $item): bool
    {
        $collection = $item->collection()?->handle();
        return $collection !== null && in_array($collection, Configuration::collections(), true);
    }

    public function visibleTo($item)
    {
        return $item instanceof Entry && $this->isValidCollection($item);
    }

    public function visibleToBulk($items)
    {
        foreach ($items as $item) {
            if ($item instanceof Entry && $this->isValidCollection($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The run method
     *
     * @return mixed
     */
    public function run($items, $values)
    {
        foreach ($items as $item) {
            GenerateSocialMediaImages::createJob($item->id());
        }
    }
}
