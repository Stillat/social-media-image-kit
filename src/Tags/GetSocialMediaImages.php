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

            $references[$imageRef] = $value[$this->fieldConfiguration->assetFieldName];
        }

        $results = [];

        foreach ($this->profileResolver->getSizes() as $profile) {
            if (! array_key_exists($profile['handle'], $references)) {
                continue;
            }

            $asset = $references[$profile['handle']];
            $imageContext = array_merge($asset->toArray(), $this->context->all());

            $attributes = Arr::get($profile, 'attributes', []);
            $attributeString = $this->createAttributeString($attributes, $imageContext);

            $additionalMetaTags = '';

            if (array_key_exists('meta_tags', $profile) && is_array($profile['meta_tags'])) {
                $tags = $profile['meta_tags'];

                foreach ($tags as $metaTag) {
                    $additionalMetaTags .= '<meta '.$this->createAttributeString($metaTag, $imageContext).' />';
                }
            }

            $assetUrl = $asset->url();

            $baseMeta = '<meta '.$attributeString.' content="'.$assetUrl.'" />';
            $baseMeta .= $additionalMetaTags;

            $results[] = [
                'url' => $assetUrl,
                'handle' => $profile['handle'],
                'name' => $profile['name'],
                'attributes' => $attributes,
                'attribute_string' => $attributeString,
                'meta_tags' => $additionalMetaTags,
                'value' => $baseMeta,
            ];
        }

        if ($this->isPair) {
            return $results;
        }

        return collect($results)->pluck('value')->implode("\n");
    }
}
