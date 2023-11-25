<?php

namespace Stillat\SocialMediaImageKit\Metadata;

use Statamic\Fields\Value;
use Statamic\Fields\Values;
use Stillat\SocialMediaImageKit\Contracts\ProfileResolver;
use Stillat\SocialMediaImageKit\GeneratorFieldConfiguration;
use Stillat\StatamicAttributeRenderer\Concerns\CreatesMetaTags;

class MetadataProvider
{
    use CreatesMetaTags;

    protected ProfileResolver $profileResolver;

    protected GeneratorFieldConfiguration $fieldConfiguration;

    protected ?array $images = null;

    public function __construct(ProfileResolver $resolver, GeneratorFieldConfiguration $config)
    {
        $this->fieldConfiguration = $config;
        $this->profileResolver = $resolver;
    }

    public function forImages(?array $images): self
    {
        $this->images = $images;

        return $this;
    }

    protected function getImages(array $context): mixed
    {
        if ($this->images != null) {
            return $this->images;
        }

        if (array_key_exists($this->fieldConfiguration->imagesFieldName, $context)) {
            return $context[$this->fieldConfiguration->imagesFieldName];
        }

        return [];
    }

    public function provide(array $context): array
    {
        $entryImages = $this->getImages($context);

        if (! $entryImages) {
            return [];
        }

        if ($entryImages instanceof Value) {
            $entryImages = $entryImages->value();
        }

        $references = [];

        /** @var Values $value */
        foreach ($entryImages as $value) {
            $imageRef = (string) $value[$this->fieldConfiguration->socialMediaPlatformType];

            if (! $imageRef) {
                continue;
            }

            $references[$imageRef] = $value[$this->fieldConfiguration->assetFieldName];
        }

        $results = [];

        foreach ($this->profileResolver->getSizes() as $profile) {
            if (! array_key_exists($profile['handle'], $references)) {
                continue;
            }

            $asset = $references[$profile['handle']];
            $imageContext = array_merge($context, $asset->toArray());

            $metaValues = [];
            $metaTags = '';

            if (array_key_exists('meta', $profile) && is_array($profile['meta'])) {
                $metaValues = $profile['meta'];

                $imageContext['url'] = $asset->absoluteUrl();
                $metaTags = implode("\n", $this->createMetaTags($metaValues, $imageContext));
            }

            $results[] = [
                'url' => $asset->url(),
                'handle' => $profile['handle'],
                'name' => $profile['name'],
                'meta' => $metaValues,
                'value' => $metaTags,
            ];
        }

        return $results;
    }

    public function getMetaTags(array $context): array
    {
        return collect($this->provide($context))->pluck('value')->all();
    }
}
