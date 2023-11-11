<?php

namespace Stillat\SocialMediaImageKit\Tags;

use Statamic\Tags\Tags;
use Stillat\SocialMediaImageKit\Metadata\MetadataProvider;

class GetSocialMediaImages extends Tags
{
    protected MetadataProvider $metadataProvider;

    public function __construct(MetadataProvider $metadataProvider)
    {
        $this->metadataProvider = $metadataProvider;
    }

    public function index()
    {
        $results = $this->metadataProvider
            ->forImages($this->params->get('images', null))
            ->provide($this->context->all());

        if ($this->isPair) {
            return $results;
        }

        return collect($results)->pluck('value')->implode("\n");
    }
}
