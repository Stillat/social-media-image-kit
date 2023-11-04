<?php

namespace Stillat\SocialMediaImageKit\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class ImageTasksTable extends Command
{
    protected $name = 'social-media-image-kit:image-tasks-table';

    protected $description = 'Create a migration for the social media image tasks database table';

    protected Filesystem $files;

    protected Composer $composer;

    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    public function handle()
    {
        $table = config('social_media_image_kit.queue.database.table', 'social_media_image_tasks');

        $this->replaceMigration(
            $this->createBaseMigration($table), $table
        );

        $this->info('Migration created successfully.');
        $this->composer->dumpAutoloads();
    }

    protected function createBaseMigration($table = 'social_media_image_tasks')
    {
        return $this->laravel['migration.creator']->create(
            'create_'.$table.'_table', $this->laravel->databasePath().'/migrations'
        );
    }

    protected function replaceMigration($path, $table)
    {
        $stub = str_replace(
            '{{table}}', $table, $this->files->get(__DIR__.'/stubs/social_media_image_tasks.stub')
        );

        $this->files->put($path, $stub);
    }
}
