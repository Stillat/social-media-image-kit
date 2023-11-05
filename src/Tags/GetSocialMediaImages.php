<?php

namespace Stillat\SocialMediaImageKit\Tags;

use Statamic\Fields\Value;
use Statamic\Fields\Values;
use Statamic\Support\Arr;
use Statamic\Tags\Tags;
use Stillat\SocialMediaImageKit\Contracts\ProfileResolver;
use Stillat\SocialMediaImageKit\GeneratorFieldConfiguration;
use Stillat\SocialMediaImageKit\Tags\Concerns\CreatesAttributeStrings;

class GetSocialMediaImages extends Tags
{
    use CreatesAttributeStrings;

    protected ProfileResolver $profileResolver;

    protected GeneratorFieldConfiguration $fieldConfiguration;

    public function __construct(ProfileResolver $resolver, GeneratorFieldConfiguration $config)
    {
        $this->fieldConfiguration = $config;
        $this->profileResolver = $resolver;
    }

    public function index()
    {
        $entryImages = $this->params->get('images', null);

        if (! $entryImages && $this->context->has($this->fieldConfiguration->imagesFieldName)) {
            $entryImages = $this->context[$this->fieldConfiguration->imagesFieldName];
        }

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

            $references[$imageRef] = $value[$this->fieldConfiguration->assetFieldName]->url();
        }

        $results = [];

        foreach ($this->profileResolver->getSizes() as $profile) {
            if (! array_key_exists($profile['handle'], $references)) {
                continue;
            }

            $attributes = Arr::get($profile, 'attributes', []);
            $attributeString = $this->createAttributeString($attributes);

            $results[] = [
                'url' => $references[$profile['handle']],
                'handle' => $profile['handle'],
                'name' => $profile['name'],
                'attributes' => $attributes,
                'attribute_string' => $attributeString,
            ];
        }

        return $results;
    }
}
