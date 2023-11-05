<?php

namespace Stillat\SocialMediaImageKit\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stillat\SocialMediaImageKit\Configuration;
use Stillat\SocialMediaImageKit\Contracts\ImageGenerator;
use Stillat\SocialMediaImageKit\Models\SocialMediaImageTasks;

class GenerateSocialMediaImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $id)
    {
    }

    protected static function getImageTaskBuilder(): Builder
    {
        return SocialMediaImageTasks::on(config('social-media-image-kit.queue.database.connection', 'mysql'));
    }

    public function handle(): void
    {
        /** @var ImageGenerator $generator */
        $generator = app(ImageGenerator::class);
        $generator->setSkipExistingImages(false);

        $generator->generateForEntry($this->id);

        if (Configuration::preventDuplicateJobs()) {
            // Delete the task record.
            self::getImageTaskBuilder()->where('entry_id', $this->id)->delete();
        }
    }

    public static function createJob(string $id): void
    {
        if (! Configuration::isEnabled()) {
            return;
        }

        // Do we already have a job for this entry?
        if (Configuration::preventDuplicateJobs()) {
            if (self::getImageTaskBuilder()->where('entry_id', $id)->exists()) {
                return;
            }

            self::getImageTaskBuilder()->create([
                'entry_id' => $id,
            ]);
        }

        GenerateSocialMediaImages::dispatch($id)
            ->onConnection(config('social-media-image-kit.queue.queue.connection', 'sync'))
            ->onQueue(config('social-media-image-kit.queue.queue.name', 'default'));
    }
}
