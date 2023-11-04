<?php

namespace Stillat\SocialMediaImageKit\Console;

use Illuminate\Console\Command;
use Statamic\Facades\AssetContainer;
use Stillat\SocialMediaImageKit\Configuration;

use function Laravel\Prompts\select;

class CreateAssetContainer extends Command
{
    protected $signature = 'social-media-image-kit:install-asset-container';

    protected $description = 'Installs the social media images asset container.';

    public function handle()
    {
        $this->info('Installing asset container...');

        $containerHandle = Configuration::assetContainer();
        $container = AssetContainer::findByHandle($containerHandle);

        if ($container != null) {
            $this->info('Asset container already exists.');

            return;
        }

        $disks = array_keys(config('filesystems.disks', []));
        $selectedDisk = select('Select a disk to use for the social media images asset container', $disks, 'local');

        if (! in_array($selectedDisk, $disks)) {
            $this->error('Invalid disk selected.');

            return;
        }

        $container = new \Statamic\Assets\AssetContainer();

        $container->handle($containerHandle)
            ->disk($selectedDisk)
            ->title('Social Media Images')
            ->saveQuietly();

        $this->info('Asset container installed.');
    }
}
