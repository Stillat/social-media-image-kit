<?php

namespace Stillat\SocialMediaImageKit;

use Statamic\Facades\Cascade;
use Statamic\Facades\Entry as EntryApi;
use Stillat\SocialMediaImageKit\Contracts\FolderNameFormatter;
use Stillat\SocialMediaImageKit\Contracts\HtmlRenderer;
use Stillat\SocialMediaImageKit\Contracts\ImageGenerator as ImageGeneratorContract;
use Stillat\SocialMediaImageKit\Contracts\ImageNameFormatter;
use Stillat\SocialMediaImageKit\Contracts\ProfileResolver;
use Stillat\StatamicTemplateResolver\StringTemplateManager;

abstract class AbstractImageGenerator implements ImageGeneratorContract
{
    protected array $sizes = [];

    protected HtmlRenderer $htmlRenderer;

    protected ProfileResolver $profileResolver;

    protected ?StringTemplateManager $templateManager = null;

    protected GeneratorFieldConfiguration $fieldConfiguration;

    protected ImageNameFormatter $nameFormatter;

    protected bool $skipExistingImages = false;

    protected bool $cleanupFiles = true;

    protected string $imageExtension = 'png';

    protected string $templatePath = '';

    protected string $tmpPath = '';

    protected FolderNameFormatter $folderNameFormatter;

    public function __construct(
        HtmlRenderer $renderer,
        ProfileResolver $profileResolver,
        GeneratorFieldConfiguration $config,
        ImageNameFormatter $nameFormatter,
        FolderNameFormatter $folderNameFormatter,
    ) {
        $this->nameFormatter = $nameFormatter;
        $this->fieldConfiguration = $config;
        $this->htmlRenderer = $renderer;
        $this->profileResolver = $profileResolver;
        $this->folderNameFormatter = $folderNameFormatter;

        $this->withDefaultSizes();
    }

    /**
     * Sets the image extension to use when generating images.
     *
     * @param  string  $extension The image extension.
     */
    public function setImageExtension(string $extension): self
    {
        $this->imageExtension = $extension;

        return $this;
    }

    /**
     * Sets the template path to use when generating images.
     *
     * @param  string  $templatePath The template path.
     */
    public function setTemplatePath(string $templatePath): self
    {
        $this->templatePath = $templatePath;
        $this->templateManager = new StringTemplateManager($templatePath);

        return $this;
    }

    /**
     * Sets the generator's field configuration.
     *
     * @param  GeneratorFieldConfiguration  $config The field configuration.
     */
    public function setFieldConfiguration(GeneratorFieldConfiguration $config): self
    {
        $this->fieldConfiguration = $config;

        return $this;
    }

    /**
     * Sets whether to clean up temporary files.
     *
     * @param  bool  $cleanUp Whether to clean up temporary files.
     */
    public function setCleanupFiles(bool $cleanUp): self
    {
        $this->cleanupFiles = $cleanUp;

        return $this;
    }

    /**
     * Sets the generators temporary directory.
     *
     * For most applications, this should point to the local storage directory.
     *
     * @param  string  $tmpDirectory The temporary directory to use.
     */
    public function setTmpDirectory(string $tmpDirectory): self
    {
        $this->tmpPath = $tmpDirectory;

        return $this;
    }

    /**
     * Configures the generator with all configured social media profiles.
     */
    public function withDefaultSizes(): self
    {
        return $this->setSizes($this->profileResolver->getSizes());
    }

    /**
     * Sets the social media profile sizes the generator should use.
     *
     * @param  array  $sizes The sizes to use.
     */
    public function setSizes(array $sizes): self
    {
        $this->sizes = $sizes;

        return $this;
    }

    /**
     * Sets whether to skip existing images.
     *
     * If set to true, the generator will not re-generate
     * social media images that already exist on an entry.
     *
     * @param  bool  $skipExisting Whether to skip existing images.
     */
    public function setSkipExistingImages(bool $skipExisting): self
    {
        $this->skipExistingImages = $skipExisting;

        return $this;
    }

    /**
     * Generates social media images for the provided entry.
     *
     * @param  string  $id The entry id.
     */
    public function generateForEntry(string $id): void
    {
        $entry = EntryApi::find($id);

        if (! $entry) {
            return;
        }

        $cascade = Cascade::instance()->hydrate()->toArray();
        $blueprint = $entry->blueprint()->handle();
        $collection = $entry->collection()->handle();

        $this->generate(
            $entry,
            $collection,
            $blueprint,
            array_merge($cascade, $entry->toArray())
        );
    }
}
