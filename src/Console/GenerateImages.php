<?php

namespace Stillat\SocialMediaImageKit\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Statamic\Facades\Entry;
use Stillat\SocialMediaImageKit\Configuration;
use Stillat\SocialMediaImageKit\Contracts\ProfileResolver;
use Stillat\SocialMediaImageKit\EntryGenerator;
use Stillat\SocialMediaImageKit\Events\GeneratedImage;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\progress;

class GenerateImages extends Command
{
    protected $signature = 'social-media-image-kit:generate-images {--regen : Regenerate existing images.}';

    protected $description = 'Generates social media images.';

    public function handle(ProfileResolver $resolver, EntryGenerator $entryGenerator)
    {
        $skipExisting = ! $this->option('regen');
        $sizes = $resolver->getSizes();
        $entryGenerator->setSize($sizes);
        $entryGenerator->setSkipExisting($skipExisting);

        $collectionsToGenerate = Configuration::collections();

        if (! $skipExisting) {

            $selected = multiselect(
                label: 'Select collections to generate images for:',
                options: $collectionsToGenerate,
                default: $collectionsToGenerate
            );

            if (count($selected) === 0) {
                $this->info('No collections selected. Exiting.');

                return;
            }

            $collectionsToGenerate = $selected;
        }

        $entries = Entry::whereInCollection($collectionsToGenerate)->all();

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
