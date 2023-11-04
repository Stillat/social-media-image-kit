<?php

namespace Stillat\SocialMediaImageKit\Tags\Concerns;

trait CreatesAttributeStrings
{
    protected function createAttributeString(array $attributes): string
    {
        return collect($attributes)
            ->map(fn ($value, $key) => $this->createAttribute($key, $value))
            ->implode(' ');
    }

    protected function createAttribute(string $key, $value): string
    {
        if (is_bool($value)) {
            return $value ? $key : '';
        }

        return sprintf('%s="%s"', $key, $value);
    }
}
