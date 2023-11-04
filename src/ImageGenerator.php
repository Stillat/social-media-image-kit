<?php

namespace Stillat\SocialMediaImageKit;

use GuzzleHttp\Psr7\MimeType;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Statamic\Assets\ReplacementFile;
use Statamic\Facades\Asset;
use Statamic\Facades\Cascade;
use Statamic\Facades\Entry;
use Stillat\SocialMediaImageKit\Contracts\HtmlRenderer;
use Stillat\SocialMediaImageKit\Contracts\ImageNameFormatter;
use Stillat\SocialMediaImageKit\Contracts\ProfileResolver;
use Stillat\SocialMediaImageKit\Events\GeneratedImage;
use Stillat\SocialMediaImageKit\Events\GeneratingImage;
use Stillat\SocialMediaImageKit\Events\GenerationFailed;
use Stillat\StatamicTemplateResolver\StringTemplateManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageGenerator
{
    protected array $sizes = [];

    protected HtmlRenderer $htmlRenderer;

    protected ProfileResolver $profileResolver;

    protected StringTemplateManager $templateManager;

    protected GeneratorFieldConfiguration $fieldConfiguration;

    protected ImageNameFormatter $nameFormatter;

    protected bool $skipExistingImages = false;

    protected bool $cleanupFiles = true;

    protected string $imageExtension = 'png';

    protected string $tmpPath = '';

    public function __construct(
        HtmlRenderer $renderer,
        ProfileResolver $profileResolver,
        GeneratorFieldConfiguration $config,
        ImageNameFormatter $nameFormatter,
        string $templatePath
    ) {
        $this->nameFormatter = $nameFormatter;
        $this->fieldConfiguration = $config;
        $this->htmlRenderer = $renderer;
        $this->profileResolver = $profileResolver;
        $this->templateManager = new StringTemplateManager($templatePath);

        $this->withDefaultSizes();
    }

    public static function isDriverReachable(): bool
    {
        $configuredDriver = Configuration::htmlRenderer();

        if (! $configuredDriver) {
            return false;
        }

        return class_exists($configuredDriver);
    }

    public function setImageExtension(string $extension): self
    {
        $this->imageExtension = $extension;

        return $this;
    }

    public function setFieldConfiguration(GeneratorFieldConfiguration $config): self
    {
        $this->fieldConfiguration = $config;

        return $this;
    }

    public function setCleanupFiles(bool $cleanUp): self
    {
        $this->cleanupFiles = $cleanUp;

        return $this;
    }

    public function setTmpDirectory(string $tmpDirectory): self
    {
        $this->tmpPath = $tmpDirectory;

        return $this;
    }

    public function withDefaultSizes(): self
    {
        return $this->setSizes($this->profileResolver->getSizes());
    }

    public function setSizes(array $sizes): self
    {
        $this->sizes = $sizes;

        return $this;
    }

    public function setSkipExistingImages(bool $skipExisting): self
    {
        $this->skipExistingImages = $skipExisting;

        return $this;
    }

    public function generateForEntry(string $id): void
    {
        $entry = Entry::find($id);

        if (! $entry) {
            return;
        }

        $cascade = Cascade::instance()->hydrate()->toArray();
        $blueprint = $entry->blueprint()->handle();
        $collection = $entry->collection()->handle();

        $this->generate(
            $entry->id(),
            $collection,
            $blueprint,
            $entry,
            array_merge($cascade, $entry->toArray())
        );
    }

    public function generate($id, $collection, $blueprint, $entry, $data): void
    {
        $html = $this->templateManager->render($collection, $blueprint, $data);

        if ($html === null) {
            return;
        }

        $updatedImages = [];
        $existingImages = collect($entry->get($this->fieldConfiguration->imagesFieldName) ?? [])
            ->keyBy($this->fieldConfiguration->assetFieldName)
            ->all();

        $filesToPreserve = collect($existingImages)->filter(function ($image) {
            return Arr::get($image, $this->fieldConfiguration->preservedFieldName, false) === true;
        })->map(function ($image) {
            return $image[$this->fieldConfiguration->assetFieldName];
        })->all();

        $changesMade = 0;

        $appPath = storage_path('app');

        foreach ($this->sizes as $size) {
            $assetPath = $this->nameFormatter->getImageName($id, $entry, $size).'.'.$this->imageExtension;

            GeneratingImage::dispatch($entry, $size);

            if (array_key_exists($assetPath, $filesToPreserve) && array_key_exists($assetPath, $existingImages)) {
                $generatedImage = $existingImages[$assetPath];

                $updatedImages[] = $generatedImage;
                GeneratedImage::dispatch($entry, $size, false);

                continue;
            }

            if ($this->skipExistingImages && array_key_exists($assetPath, $existingImages)) {
                $generatedImage = $existingImages[$assetPath];

                $updatedImages[] = $generatedImage;
                GeneratedImage::dispatch($entry, $size, false);

                continue;
            }

            $path = $this->tmpPath.'/'.$assetPath;

            $imageDetails = new ImageDetails();
            $imageDetails->width = $size['width'];
            $imageDetails->height = $size['height'];

            if (! $this->htmlRenderer->fromHtml($path, $html, $imageDetails)) {
                GenerationFailed::dispatch($entry, $size);

                continue;
            }

            $assetId = $this->fieldConfiguration->assetContainer.'::'.$assetPath;
            /** @var \Statamic\Assets\Asset $asset */
            $asset = Asset::findById($assetId);

            if ($asset !== null) {
                $replacementPath = $path;

                if (Str::startsWith($replacementPath, $appPath)) {
                    $replacementPath = Str::after($replacementPath, $appPath);
                }

                $asset->reupload(new ReplacementFile($replacementPath));
            } else {
                $asset = Asset::make()->container($this->fieldConfiguration->assetContainer)->path($assetPath);
                $asset->save();

                $asset->upload(new UploadedFile($path, basename($path), MimeType::fromExtension($this->imageExtension)));
            }

            if ($this->cleanupFiles && file_exists($path)) {
                @unlink($path);
            }

            $generatedImage = [
                'id' => 's'.Str::random(8),
                $this->fieldConfiguration->assetFieldName => $asset->path(),
                $this->fieldConfiguration->socialMediaPlatformType => $size['handle'],
            ];

            $updatedImages[] = $generatedImage;

            GeneratedImage::dispatch($entry, $size, true);

            $changesMade++;
        }

        if ($changesMade == 0) {
            return;
        }

        $entry->set($this->fieldConfiguration->imagesFieldName, $updatedImages);
        $entry->saveQuietly();
    }
}
