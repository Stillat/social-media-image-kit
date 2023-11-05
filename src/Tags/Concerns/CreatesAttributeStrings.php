<?php

namespace Stillat\SocialMediaImageKit\Tags\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CreatesAttributeStrings
{
    protected function createAttributeString(array $attributes, array $context): string
    {
        return collect($attributes)
            ->map(fn ($value, $key) => $this->createAttribute($key, $value, $context))
            ->implode(' ');
    }

    protected function createAttribute(string $key, $value, array $context): string
    {
        if (is_string($value) && Str::startsWith($value, '@')) {
            $value = Arr::get($context, Str::substr($value, 1), '');
        }

        if (is_bool($value)) {
            return $value ? $key : '';
        }

        $value = (string) $value;

        return sprintf('%s="%s"', $key, $value);
    }
}
