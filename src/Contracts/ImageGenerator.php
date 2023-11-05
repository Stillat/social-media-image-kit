<?php

namespace Stillat\SocialMediaImageKit\Contracts;

use Statamic\Contracts\Entries\Entry;
use Stillat\SocialMediaImageKit\GeneratorFieldConfiguration;

interface ImageGenerator
{
    /**
     * Sets the image extension to use when generating images.
     *
     * @param  string  $extension The image extension.
     */
    public function setImageExtension(string $extension): self;

    /**
     * Sets the template path to use when generating images.
     *
     * @param  string  $templatePath The template path.
     */
    public function setTemplatePath(string $templatePath): self;

    /**
     * Sets the generator's field configuration.
     *
     * @param  GeneratorFieldConfiguration  $config The field configuration.
     */
    public function setFieldConfiguration(GeneratorFieldConfiguration $config): self;

    /**
     * Sets whether to clean up temporary files.
     *
     * @param  bool  $cleanUp Whether to clean up temporary files.
     */
    public function setCleanupFiles(bool $cleanUp): self;

    /**
     * Sets the generators temporary directory.
     *
     * For most applications, this should point to the local storage directory.
     *
     * @param  string  $tmpDirectory The temporary directory to use.
     */
    public function setTmpDirectory(string $tmpDirectory): self;

    /**
     * Configures the generator with all configured social media profiles.
     */
    public function withDefaultSizes(): self;

    /**
     * Sets the social media profile sizes the generator should use.
     *
     * @param  array  $sizes The sizes to use.
     */
    public function setSizes(array $sizes): self;

    /**
     * Sets whether to skip existing images.
     *
     * If set to true, the generator will not re-generate
     * social media images that already exist on an entry.
     *
     * @param  bool  $skipExisting Whether to skip existing images.
     */
    public function setSkipExistingImages(bool $skipExisting): self;

    /**
     * Generates social media images for the provided entry.
     *
     * @param  string  $id The entry id.
     */
    public function generateForEntry(string $id): void;

    /**
     * Generates social media images for the provided entry.
     *
     * @param  Entry  $entry The entry to generate images for.
     * @param  string  $collection The collection handle.
     * @param  string  $blueprint The blueprint handle.
     * @param  array  $data The template data.
     */
    public function generate(Entry $entry, string $collection, string $blueprint, array $data): void;
}
