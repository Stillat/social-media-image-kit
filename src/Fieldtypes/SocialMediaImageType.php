<?php

namespace Stillat\SocialMediaImageKit\Fieldtypes;

use Statamic\Fieldtypes\Select;
use Stillat\SocialMediaImageKit\Contracts\ProfileResolver;

class SocialMediaImageType extends Select
{
    public function preload()
    {
        $options = [];

        /** @var ProfileResolver $configResolver */
        $configResolver = app(ProfileResolver::class);

        foreach ($configResolver->getSizes() as $size) {
            $name = $size['name'];
            $handle = $size['handle'];

            $display = ucfirst($name).' ('.$size['width'].'x'.$size['height'].')';

            $options[$handle] = $display;
        }

        return [
            'sizes' => $options,
        ];
    }
}
