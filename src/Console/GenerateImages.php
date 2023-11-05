<?php

namespace Stillat\SocialMediaImageKit\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Statamic\Facades\Cascade;
use Statamic\Facades\Entry;
use Stillat\SocialMediaImageKit\Configuration;
use Stillat\SocialMediaImageKit\Contracts\ImageGenerator;
use Stillat\SocialMediaImageKit\Contracts\ProfileResolver;
use Stillat\SocialMediaImageKit\Events\GeneratedImage;

use function Laravel\Prompts\progress;

class GenerateImages extends Command
{
    protected $signature = 'social-media-image-kit:generate-images {--regen}';

    protected $description = 'Generates social media images.';

    public function handle(ProfileResolver $resolver)
    {
        $skipExisting = ! $this->option('regen');
        $sizes = $resolver->getSizes();
        $entries = Entry::whereInCollection(Configuration::collections())->all();
        $cascade = Cascade::instance()->hydrate()->toArray();

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

        foreach ($entries as $entry) {
            $blueprint = $entry->blueprint()->handle();
            $collection = $entry->collection()->handle();

            /** @var ImageGenerator $generator */
            $generator = app(ImageGenerator::class);
            $generator->setSkipExistingImages($skipExisting);

            $generator->generate(
                $entry,
                $collection,
                $blueprint,
                array_merge($cascade, $entry->toArray())
            );
        }

        $progress->finish();
    }
}
