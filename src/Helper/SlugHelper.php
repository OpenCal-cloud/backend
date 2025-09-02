<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\String\Slugger\AsciiSlugger;

class SlugHelper
{
    public function __construct(
        private readonly string $locale,
    ) {
    }

    public function slugify(string $value): string
    {
        $slugger = new AsciiSlugger();

        return $slugger->slug($value, '-', $this->locale)->lower()->toString();
    }
}
