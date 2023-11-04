<?php

namespace Stillat\SocialMediaImageKit\Rendering;

use Closure;
use Spatie\Browsershot\Browsershot;

class BrowsershotFactory
{
    private ?Closure $configurationMethod = null;

    public function applyConfiguration(Browsershot $browsershot)
    {
        if ($this->configurationMethod === null) {
            return;
        }

        $this->configurationMethod->call($this, $browsershot);
    }

    public function configureWith(Closure $configurationMethod): self
    {
        $this->configurationMethod = $configurationMethod;

        return $this;
    }
}
