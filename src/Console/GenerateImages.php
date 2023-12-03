<?php

namespace Stillat\SocialMediaImageKit\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Statamic\Facades\Collection as CollectionApi;
use Statamic\Facades\Entry;
use Stillat\SocialMediaImageKit\Configuration;
use Stillat\SocialMediaImageKit\Contracts\ProfileResolver;
use Stillat\SocialMediaImageKit\EntryGenerator;
use Stillat\SocialMediaImageKit\Events\GeneratedImage;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\progress;

class GenerateImages extends Command
{
    protected $signature = 'social-media-image-kit:generate-images {entry?} {--regen : Regenerate existing images.} {--collection=-1 : The collections to generate images for.}';

    protected $description = 'Generates social media images.';

    public function handle(ProfileResolver $resolver, EntryGenerator $entryGenerator)
    {
        $skipExisting = ! $this->option('regen');
        $sizes = $resolver->getSizes();
        $entryGenerator->setSize($sizes);
        $entryGenerator->setSkipExisting($skipExisting);
        $entries = [];

        $collection = $this->option('collection');

        if ($collection === '-1') {
            $singleEntry = $this->argument('entry');

            if ($singleEntry !== null) {
                $entries = [Entry::find($singleEntry)];
            } else {
                $collectionsToGenerate = Configuration::collections();
                $collections = collect(CollectionApi::all())->sortBy('title')->all();

                $options = ['*' => 'All collections'];

                foreach ($collections as $collection) {
                    if (! in_array($collection->handle(), $collectionsToGenerate)) {
                        continue;
                    }

                    $options[$collection->handle()] = $collection->title();
                }

                $selected = multiselect(
                    label: 'Select collections to generate images for:',
                    options: $options,
                );

                if (count($selected) === 0) {
                    $this->info('No collections selected. Exiting.');

                    return;
                }

                if (! in_array('*', $selected)) {
                    $collectionsToGenerate = $selected;
                }

                $entries = Entry::whereInCollection($collectionsToGenerate)->all();
            }
        } else {
            if ($collection == '*') {
                $entries = Entry::whereInCollection(Configuration::collections())->all();
            } else {
                $collectionsToGenerate = array_map('trim', explode(',', $collection));
                $entries = Entry::whereInCollection($collectionsToGenerate)->all();
            }
        }

        if (count($entries) === 0) {
            $this->info('No entries found. Exiting.');

            return;
        }

        $progress = progress(
            label: 'Preparing to generate images...',
            steps: count($entries) * count($sizes),
            hint: 'This may take a while.'
        );

        Event::listen(GeneratedImage::class, function (GeneratedImage $event) use ($progress) {
            $title = (string) $event->entry->get('title');
            $imageName = $event->size['name'];
            $width = $event->size['width'];
            $height = $event->size['height'];

            $progress->label(
                "Generating: {$title} ({$imageName} - {$width}x{$height})"
            )->advance();
        });

        $entryGenerator->generate($entries);

        $progress->finish();
    }
}
