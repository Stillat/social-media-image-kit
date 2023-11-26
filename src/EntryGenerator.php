<?php

namespace Stillat\SocialMediaImageKit;

use Statamic\Facades\Cascade;
use Stillat\SocialMediaImageKit\Contracts\ImageGenerator;

class EntryGenerator
{
    protected array $sizes = [];

    protected bool $skipExisting = true;

    public function setSize(array $sizes): self
    {
        $this->sizes = $sizes;

        return $this;
    }

    public function getSizes(): array
    {
        return $this->sizes;
    }

    public function setSkipExisting(bool $skipExisting): self
    {
        $this->skipExisting = $skipExisting;

        return $this;
    }

    public function getSkipExisting(): bool
    {
        return $this->skipExisting;
    }

    public function generate(array $entries): void
    {
        $cascade = Cascade::instance()->hydrate()->toArray();
        foreach ($entries as $entry) {
            $blueprint = $entry->blueprint()->handle();
            $collection = $entry->collection()->handle();

            /** @var ImageGenerator $generator */
            $generator = app(ImageGenerator::class);
            $generator->setSkipExistingImages($this->skipExisting);

            $generator->generate(
                $entry,
                $collection,
                $blueprint,
                array_merge($cascade, $entry->toAugmentedArray())
            );
        }
    }
}
