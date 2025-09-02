<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Helper;

use App\Helper\SlugHelper;
use PHPUnit\Framework\TestCase;

class SlugHelperTest extends TestCase
{
    public function testSlugifyGerman(): void
    {
        $helper = new SlugHelper('de_DE');
        $slug   = $helper->slugify('Das ist großer schöner See!');

        self::assertSame(
            'das-ist-grosser-schoener-see',
            $slug,
        );
    }

    public function testSlugifyEnglish(): void
    {
        $helper = new SlugHelper('en_GB');
        $slug   = $helper->slugify('That\'s a big, beautiful lake!');

        self::assertSame(
            'that-s-a-big-beautiful-lake',
            $slug,
        );
    }
}
